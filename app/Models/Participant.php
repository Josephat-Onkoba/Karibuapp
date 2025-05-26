<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Participant extends Model
{
    use HasFactory, Notifiable;
    
    // Payment amount constants
    const EXHIBITOR_FEE = 30000.00;
    const PRESENTER_NON_STUDENT_FEE = 6000.00;
    const PRESENTER_STUDENT_FEE = 4000.00;
    const PRESENTER_INTERNATIONAL_FEE = 100.00; // USD
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'job_title',
        'organization',
        'student_admission_number',
        'staff_number',
        'role',
        'category',
        'presenter_type',
        'payment_status',
        'payment_confirmed',
        'payment_amount',
        'eligible_days',
        'registered_by_user_id'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payment_confirmed' => 'boolean',
            'payment_amount' => 'decimal:2',
            'eligible_days' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
    
    /**
     * Get the check-ins for the participant.
     */
    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }
    
    /**
     * Check if the participant has been checked in for a specific day.
     *
     * @param int $conferenceDayId
     * @return bool
     */
    public function isCheckedInForDay(int $conferenceDayId): bool
    {
        return $this->checkIns()
            ->where('conference_day_id', $conferenceDayId)
            ->exists();
    }
    
    /**
     * Get the active ticket for the participant.
     */
    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class)->where('active', true);
    }
    
    /**
     * Get all tickets for the participant, including inactive ones.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
    
    /**
     * Get the user who registered this participant.
     */
    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }
    
    /**
     * Get the required payment amount based on category and type.
     *
     * @return float|null
     */
    public function getRequiredPaymentAmount(): ?float
    {
        switch ($this->category) {
            case 'exhibitor':
                return self::EXHIBITOR_FEE;
            case 'presenter':
                switch ($this->presenter_type) {
                    case 'non_student':
                        return self::PRESENTER_NON_STUDENT_FEE;
                    case 'student':
                        return self::PRESENTER_STUDENT_FEE;
                    case 'international':
                        return self::PRESENTER_INTERNATIONAL_FEE;
                    default:
                        return null;
                }
            default:
                return null;
        }
    }
    
    /**
     * Get the eligible days based on category.
     *
     * @return int|null
     */
    public function getDefaultEligibleDays(): ?int
    {
        switch ($this->category) {
            case 'exhibitor':
            case 'presenter':
                return 3; // Full conference period
            default:
                return null;
        }
    }
    
    /**
     * Get the meal servings for this participant.
     */
    public function mealServings(): HasMany
    {
        return $this->hasMany(MealServing::class);
    }
    
    /**
     * Check if the participant has been served a specific meal on a specific day.
     *
     * @param int $mealTypeId
     * @param int $conferenceDayId
     * @return bool
     */
    public function hasBeenServedMeal(int $mealTypeId, int $conferenceDayId): bool
    {
        return $this->mealServings()
            ->where('meal_type_id', $mealTypeId)
            ->where('conference_day_id', $conferenceDayId)
            ->exists();
    }

    /**
     * Route notifications for the TalkSasa channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForTalkSasa($notification)
    {
        return $this->phone_number;
    }
} 