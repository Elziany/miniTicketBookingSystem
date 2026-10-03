<?php

namespace App\Observers;

use App\Enum\ReservationStatus;
use App\Models\Reservation;
use App\Traits\AuditLogTrait;

class ReservationObserver
{
    use AuditLogTrait;

    public function updated(Reservation $reservation): void
    {
        $trackedFields = ['status', 'seat_id', 'price_paid', 'refunded_amount', 'qr_code_path'];

        if (! $reservation->wasChanged($trackedFields)) {
            return;
        }

        $changes = collect($reservation->getChanges())
            ->only($trackedFields)
            ->all();

        $oldValues = [];
        foreach (array_keys($changes) as $field) {
            $oldValues[$field] = $reservation->getOriginal($field);
        }

        $status = $reservation->status instanceof ReservationStatus
            ? $reservation->status->value
            : $reservation->status;

        $this->logAudit(
            model: $reservation,
            action: 'reservation.'.$status,
            oldValues: $oldValues,
            newValues: $changes,
            authoritySource: $this->resolveAuthoritySource()
        );
    }

    protected function resolveAuthoritySource(): string
    {
        $user = auth()->user();

        if (! $user) {
            return 'direct_permission';
        }

        if ($user->hasRole('Agent') || $user->hasRole('Hall Manager')) {
            return 'hierarchy';
        }

        if (app(\App\Services\HallAccessService::class)->hasGlobalPermission($user, 'Update:Reservation')) {
            return 'global_permission';
        }

        return 'direct_permission';
    }
}
