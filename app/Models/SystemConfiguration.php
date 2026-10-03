<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['hold_duration_minutes', 'value', 'key'])]
class SystemConfiguration extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    public static function holdDurationMinutes(): int
    {
        $row = static::query()->first();

        if ($row?->hold_duration_minutes) {
            return max(1, (int) $row->hold_duration_minutes);
        }

        $fromValue = $row?->value['hold_duration_minutes'] ?? null;

        return max(1, (int) ($fromValue ?? 15));
    }
}
