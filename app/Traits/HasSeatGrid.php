<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait HasSeatGrid
{
    public static function defaultSeatLabel(int $row, int $col): string
    {
        return chr(65 + $row) . ($col + 1);
    }

    public function seatGridLabels(): array
    {
        return $this->seats
            ->mapWithKeys(fn ($seat) => [
                ($seat->position_y - 1) . '_' . ($seat->position_x - 1) => $seat->label,
            ])
            ->all();
    }
    public function syncSeatGrid(array $labels): void
    {
        if ($this->layout_type !== 'grid') {
            return;
        }

        $rows = (int) $this->row_count;
        $cols = (int) $this->column_count;

        $desired = [];
        $seen = [];

        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                $label = trim((string) ($labels["{$r}_{$c}"] ?? ''));
                $label = $label !== '' ? $label : static::defaultSeatLabel($r, $c);

                $key = mb_strtolower($label);

                if (isset($seen[$key])) {
                    throw ValidationException::withMessages([
                        "data.seat_labels.{$r}_{$c}" => "Duplicate seat label \"{$label}\".",
                    ]);
                }

                $seen[$key] = true;
                $desired[] = ['y' => $r + 1, 'x' => $c + 1, 'label' => $label];
            }
        }

        DB::transaction(function () use ($desired, $rows, $cols) {
            $this->seats()
                ->where(fn($q) => $q->where('position_y', '>', $rows)
                    ->orWhere('position_x', '>', $cols))
                ->delete();

            $this->seats()->update(['label' => DB::raw("CONCAT('__tmp_', id)")]);

            foreach ($desired as $seat) {
                $this->seats()->updateOrCreate(
                    ['position_y' => $seat['y'], 'position_x' => $seat['x']],
                    ['label' => $seat['label']],
                );
            }
        });
    }
}