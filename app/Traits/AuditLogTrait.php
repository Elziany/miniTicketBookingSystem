<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait AuditLogTrait
{
   
    public function logAudit(
        Model $model,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $authoritySource = 'global_permission',
        bool $isUnauthorized = false
    ): AuditLog {
        return AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'authority_source' => $authoritySource,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'is_unauthorized_attempt' => $isUnauthorized,
            'created_at' => now(),
        ]);
    }
}