<?php

namespace App\Models;

use App\Enum\ReservationStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'event_id',
        'seat_id',
        'user_id',
        'reservation_reference',
        'price_paid',
        'status',
        'qr_code_path',
        'expires_at',
        'refunded_amount',
        'cancellation_reason',
        'rejection_reason',
        'feedback_requested_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_paid'      => 'decimal:2',
            'refunded_amount' => 'decimal:2',
            'expires_at'      => 'datetime',
            'feedback_requested_at' => 'datetime',
            'status' => ReservationStatus::class,
        ];
    }

    /**
     * Determine if reservation is currently active using Attribute class.
     */
    protected function isConfirmed(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === ReservationStatus::CONFIRMED,
        );
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class);
    }
    public function reminders(): HasMany {
        return $this->hasMany(ReservationReminders::class);
    }
}
