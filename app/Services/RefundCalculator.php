<?php

namespace App\Services;

use App\Models\Event;

class RefundCalculator
{
    public function percentForEvent(Event $event): float
    {
        $hoursUntilStart = now()->diffInHours($event->start_time, false);
        $policy = $event->custom_refund_policy;

        if (! is_array($policy) || $policy === []) {
            return $hoursUntilStart > 0 ? 100.0 : 0.0;
        }

        usort($policy, function (array $a, array $b): int {
            return ((int) ($b['hours_before'] ?? 0)) <=> ((int) ($a['hours_before'] ?? 0));
        });

        foreach ($policy as $tier) {
            $hoursBefore = (int) ($tier['hours_before'] ?? 0);
            if ($hoursUntilStart >= $hoursBefore) {
                return (float) ($tier['percent'] ?? 0);
            }
        }

        return 0.0;
    }

    public function amount(float $pricePaid, Event $event): string
    {
        $percent = $this->percentForEvent($event);

        return number_format($pricePaid * ($percent / 100), 2, '.', '');
    }
}
