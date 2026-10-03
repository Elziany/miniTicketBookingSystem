<?php

namespace App\Observers;

use App\Enum\CheckInType;
use App\Models\Attendance;
use App\Traits\AuditLogTrait;

class AttendanceObserver
{
    use AuditLogTrait;

    public function created(Attendance $attendance): void
    {
        $type = $attendance->check_in_type instanceof CheckInType
            ? $attendance->check_in_type
            : CheckInType::from((string) $attendance->check_in_type);

        $action = $type === CheckInType::MANUAL
            ? 'attendance.manual'
            : 'attendance.qr_scan';

        $this->logAudit(
            model: $attendance,
            action: $action,
            oldValues: null,
            newValues: [
                'reservation_id' => $attendance->reservation_id,
                'checked_in_by' => $attendance->checked_in_by,
                'check_in_type' => $type->value,
                'manual_check_in_note' => $attendance->manual_check_in_note,
                'checked_in_at' => $attendance->checked_in_at?->toDateTimeString(),
            ],
            authoritySource: $type === CheckInType::MANUAL ? 'hierarchy' : 'direct_permission'
        );
    }
}
