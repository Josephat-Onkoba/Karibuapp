<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckIn extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'participant_id',
        'conference_day_id',
        'checked_by_user_id',
        'checked_in_at',
        'notes'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
    
    /**
     * Get the participant associated with the check-in.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }
    
    /**
     * Get the conference day associated with the check-in.
     */
    public function conferenceDay(): BelongsTo
    {
        return $this->belongsTo(ConferenceDay::class);
    }
    
    /**
     * Get the user who performed the check-in.
     */
    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by_user_id');
    }
} 