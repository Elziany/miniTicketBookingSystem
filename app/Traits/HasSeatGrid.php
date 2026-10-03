<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

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

        DB::transaction(function () use ($labels, $rows, $cols) {
            for ($r = 0; $r < $rows; $r++) {
                for ($c = 0; $c < $cols; $c++) {
                    $label = trim((string) ($labels["{$r}_{$c}"] ?? ''));

                    $this->seats()->updateOrCreate(
                        ['position_y' => $r + 1, 'position_x' => $c + 1 , 'hall_id' => $this->id],
                        ['label' => $label !== '' ? $label : static::defaultSeatLabel($r, $c)],
                    );
                }
            }

            // Remove seats that fell outside a shrunken grid.
            $this->seats()
                ->where(fn ($q) => $q->where('position_y', '>', $rows)->orWhere('position_x', '>', $cols))
                ->delete();
        });
    }
}