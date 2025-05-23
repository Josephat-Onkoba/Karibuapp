<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConferenceDay extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'date',
        'description',
        'active'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
    
    /**
     * Get all check-ins for this conference day.
     */
    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }
    
    /**
     * Get the total number of check-ins for this day.
     *
     * @return int
     */
    public function getCheckInsCountAttribute(): int
    {
        return $this->checkIns()->count();
    }
    
    /**
     * Check if this conference day is today.
     *
     * @return bool
     */
    public function isToday(): bool
    {
        return $this->date->isToday();
    }
    
    /**
     * Get today's conference day, or null if there is no day for today.
     *
     * @return self|null
     */
    public static function getToday(): ?self
    {
        return self::where('date', now()->toDateString())
            ->where('active', true)
            ->first();
    }
} 