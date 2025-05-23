<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    use HasFactory;
    
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
        'payment_status',
        'payment_confirmed',
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
        return $this->hasMany(Ticket::class)->orderBy('created_at', 'desc');
    }
    
    /**
     * Get the user who registered this participant.
     */
    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
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
} 