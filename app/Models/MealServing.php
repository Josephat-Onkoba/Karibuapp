<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealServing extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'participant_id',
        'meal_type_id',
        'conference_day_id',
        'served_by_user_id',
        'ticket_number',
        'served_at',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'served_at' => 'datetime',
    ];

    /**
     * Get the participant who was served.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Get the meal type that was served.
     */
    public function mealType(): BelongsTo
    {
        return $this->belongsTo(MealType::class);
    }

    /**
     * Get the conference day when this meal was served.
     */
    public function conferenceDay(): BelongsTo
    {
        return $this->belongsTo(ConferenceDay::class);
    }

    /**
     * Get the user who served this meal.
     */
    public function servedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by_user_id');
    }
}
