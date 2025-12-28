<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Transaction type constants
     */
    const TYPE_IN = 'in';
    const TYPE_WITHDRAW = 'withdraw';

    /**
     * Transaction status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'type',
        'amount',
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
            'amount' => 'decimal:2',
            'type' => 'string',
            'status' => 'string',
        ];
    }

    /**
     * Get the booking for this transaction.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Check if transaction has a specific type.
     */
    public function hasType(string $type): bool
    {
        return $this->type === $type;
    }

    /**
     * Check if transaction has a specific status.
     */
    public function hasStatus(string $status): bool
    {
        return $this->status === $status;
    }

    /**
     * Check if transaction is income type.
     */
    public function isIncome(): bool
    {
        return $this->hasType(self::TYPE_IN);
    }

    /**
     * Check if transaction is withdrawal type.
     */
    public function isWithdrawal(): bool
    {
        return $this->hasType(self::TYPE_WITHDRAW);
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->hasStatus(self::STATUS_PENDING);
    }

    /**
     * Check if transaction is completed.
     */
    public function isCompleted(): bool
    {
        return $this->hasStatus(self::STATUS_COMPLETED);
    }

    /**
     * Check if transaction has failed.
     */
    public function isFailed(): bool
    {
        return $this->hasStatus(self::STATUS_FAILED);
    }
}
