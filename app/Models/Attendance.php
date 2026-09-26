<?php

namespace App\Models;

use App\Enum\CheckInType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'checked_in_by',
        'check_in_type',
        'manual_check_in_note',
        'checked_in_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
            'check_in_type' => CheckInType::class
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function checkedInByAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }
}
