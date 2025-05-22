<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'start_time',
        'end_time',
        'active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'active' => 'boolean',
    ];

    /**
     * Get the servings for this meal type.
     */
    public function servings(): HasMany
    {
        return $this->hasMany(MealServing::class);
    }
    
    /**
     * Check if this meal type is currently being served.
     */
    public function isCurrentlyServed(): bool
    {
        if (!$this->active) {
            return false;
        }

        $now = now()->format('H:i:s');
        return $now >= $this->start_time && $now <= $this->end_time;
    }
}
