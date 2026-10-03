<?php

namespace App\Models;

use App\Enum\EventStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'hall_id',
        'name',
        'description',
        'start_time',
        'end_time',
        'status',
        'approval_mode',
        'approval_window_hours',
        'pricing_mode',
        'custom_refund_policy',
        'cancellation_reason',
        'started_at',
        'ended_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time'            => 'datetime',
            'end_time'              => 'datetime',
            'started_at'            => 'datetime',
            'ended_at'              => 'datetime',
            'approval_window_hours' => 'integer',
            'custom_refund_policy'  => 'array',
            'status'                => EventStatus::class,
            'approval_mode'         => \App\Enum\ApprovalMode::class,
        ];
    }

    /**
     * Check if event is currently active/ongoing using Attribute class.
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === EventStatus::STARTED,
        );
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function seatPrices(): HasMany
    {
        return $this->hasMany(EventSeatPrice::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(EventFeedback::class);
    }
}
