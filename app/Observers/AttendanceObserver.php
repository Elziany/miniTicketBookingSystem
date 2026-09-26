<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Traits\AuditLogTrait;

class AttendanceObserver
{
    use AuditLogTrait;

    public function created(Attendance $attendance): void
    {
        $action = $attendance->method === 'manual'
            ? 'attendance.manaual_approve'
            : 'attendance.qr_scan';

        $this->logAudit(
            model: $attendance,
            action: $action,
            oldValues: null,
            newValues: [
                'reservation_id' => $attendance->reservation_id,
                'event_id' => $attendance->event_id,
                'user_id' => $attendance->user_id,
                'scanned_by_id' => $attendance->scanned_by_id,
                'method' => $attendance->method,
                'notes' => $attendance->notes,
                'checked_in_at' => $attendance->checked_in_at?->toDateTimeString(),
            ],
            authoritySource: $attendance->method === 'manual' ? 'hierarchy' : 'global_permission'
        );
    }
}