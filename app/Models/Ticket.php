<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_number',
        'participant_id',
        'registered_by_user_id',
        'day1_valid',
        'day2_valid',
        'day3_valid',
        'active',
        'expiration_date'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'day1_valid' => 'boolean',
            'day2_valid' => 'boolean',
            'day3_valid' => 'boolean',
            'active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'expiration_date' => 'datetime',
        ];
    }
    
    /**
     * Get the participant associated with the ticket.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }
    
    /**
     * Get the user who registered this ticket.
     */
    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }
    
    /**
     * Generate a unique ticket number.
     *
     * @return string
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'ZU-RIW25-';
        $random = mt_rand(1000, 9999);
        
        $ticketNumber = $prefix . $random;
        
        // Check if this ticket number already exists
        if (self::where('ticket_number', $ticketNumber)->exists()) {
            // Try again with a different random number
            return self::generateTicketNumber();
        }
        
        return $ticketNumber;
    }
    
    /**
     * Check if the ticket is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        if (!$this->expiration_date) {
            return false;
        }
        
        return now() > $this->expiration_date;
    }
    
    /**
     * Calculate the expiration date based on ticket validity.
     *
     * @return \Carbon\Carbon
     */
    public static function calculateExpirationDate($day1Valid, $day2Valid, $day3Valid): Carbon
    {
        // Set timezone to East Africa Time (Nairobi)
        $timezone = 'Africa/Nairobi';
        
        // Get current date in the specified timezone
        $today = Carbon::now()->setTimezone($timezone)->startOfDay();
        
        // Get conference days
        $conferenceDays = ConferenceDay::where('active', true)
            ->orderBy('date')
            ->get();
            
        // Determine the last day this ticket is valid for
        $lastDay = null;
        
        if ($day3Valid && isset($conferenceDays[2])) {
            $lastDay = $conferenceDays[2]->date;
        } elseif ($day2Valid && isset($conferenceDays[1])) {
            $lastDay = $conferenceDays[1]->date;
        } elseif ($day1Valid && isset($conferenceDays[0])) {
            $lastDay = $conferenceDays[0]->date;
        } else {
            // Default to today if no specific day is valid
            $lastDay = $today;
        }
        
        // Set expiration to 10:00 PM EAT on the last valid day
        return Carbon::parse($lastDay)->setTimezone($timezone)->setTime(22, 0, 0);
    }
    
    /**
     * Calculate how many days this ticket is valid for.
     *
     * @return int
     */
    public function validForDays(): int
    {
        $days = 0;
        if ($this->day1_valid) $days++;
        if ($this->day2_valid) $days++;
        if ($this->day3_valid) $days++;
        
        return $days;
    }
    
    /**
     * Get human-readable validity status.
     *
     * @return string
     */
    public function getValidityStatus(): string
    {
        if (!$this->active) {
            return 'Inactive';
        }
        
        if ($this->isExpired()) {
            return 'Expired';
        }
        
        $days = $this->validForDays();
        return 'Valid for ' . $days . ' ' . ($days === 1 ? 'day' : 'days');
    }
} 