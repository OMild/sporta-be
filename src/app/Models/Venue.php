<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    /**
     * Venue status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'name',
        'address',
        'description',
        'facilities',
        'open_hour',
        'close_hour',
        'price_per_hour',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'open_hour' => 'datetime:H:i',
            'close_hour' => 'datetime:H:i',
            'price_per_hour' => 'decimal:2',
            'status' => 'string',
        ];
    }

    /**
     * Get the owner of this venue.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the bookings for this venue.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if venue has a specific status.
     */
    public function hasStatus(string $status): bool
    {
        return $this->status === $status;
    }

    /**
     * Check if venue is active.
     */
    public function isActive(): bool
    {
        return $this->hasStatus(self::STATUS_ACTIVE);
    }

    /**
     * Check if venue is pending approval.
     */
    public function isPending(): bool
    {
        return $this->hasStatus(self::STATUS_PENDING);
    }

    /**
     * Check if venue is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->hasStatus(self::STATUS_SUSPENDED);
    }
}
