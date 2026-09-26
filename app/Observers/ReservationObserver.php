<?php

namespace App\Observers;

use App\Models\Reservation;
use App\Traits\AuditLogTrait;

class ReservationObserver
{
    use AuditLogTrait;

    public function updated(Reservation $reservation): void
    {
        $trackedFields = ['status', 'total_amount', 'seat_id', 'agent_id'];

        if (! $reservation->wasChanged($trackedFields)) {
            return;
        }

        $changes = $reservation->getChanges();
        $oldValues = [];
        foreach (array_keys($changes) as $field) {
            $oldValues[$field] = $reservation->getOriginal($field);
        }

        $this->logAudit(
            model: $reservation,
            action: 'reservation.' . $reservation->status,
            oldValues: $oldValues,
            newValues: $changes,
            authoritySource: $this->resolveAuthoritySource()
        );
    }

    protected function resolveAuthoritySource(): string
    {
        $user = auth()->user();

        if (! $user) {
            return 'system';
        }

        if ($user->hasRole('Agent') || $user->hasRole('Hall Manager')) {
            return 'hierarchy';
        }

        return 'global_permission';
    }
}