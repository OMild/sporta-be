<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * Booking status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_BLOCKED = 'blocked';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'venue_id',
        'booking_date',
        'start_time',
        'end_time',
        'total_price',
        'status',
        'payment_proof',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'start_time' => 'string',
            'end_time' => 'string',
            'total_price' => 'decimal:2',
            'status' => 'string',
        ];
    }

    /**
     * Get the user who made this booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the venue for this booking.
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the transaction for this booking.
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    /**
     * Check if booking has a specific status.
     */
    public function hasStatus(string $status): bool
    {
        return $this->status === $status;
    }

    /**
     * Check if booking is pending.
     */
    public function isPending(): bool
    {
        return $this->hasStatus(self::STATUS_PENDING);
    }

    /**
     * Check if booking is paid.
     */
    public function isPaid(): bool
    {
        return $this->hasStatus(self::STATUS_PAID);
    }

    /**
     * Check if booking is completed.
     */
    public function isCompleted(): bool
    {
        return $this->hasStatus(self::STATUS_COMPLETED);
    }

    /**
     * Check if booking is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->hasStatus(self::STATUS_CANCELLED);
    }

    /**
     * Check if booking is blocked.
     */
    public function isBlocked(): bool
    {
        return $this->hasStatus(self::STATUS_BLOCKED);
    }
}
