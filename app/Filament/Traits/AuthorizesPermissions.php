<?php

namespace App\Filament\Traits;

trait AuthorizesPermissions
{

    abstract static function getPermissionDomain(): string;

    public static function canViewAny(): bool
    {
        $domain = static::getPermissionDomain();
        $permission = config("roles_permissions.permissions.view_any_{$domain}");

        return $permission && auth()->user()?->can($permission);
    }

    public static function canCreate(): bool
    {
        $domain = static::getPermissionDomain();
        $permission = config("roles_permissions.permissions.create_{$domain}");

        return $permission && auth()->user()?->can($permission);
    }

    public static function canEdit($record): bool
    {
        $domain = static::getPermissionDomain();
        $permission = config("roles_permissions.permissions.edit_{$domain}");

        return $permission && auth()->user()?->can($permission);
    }

    public static function canDelete($record): bool
    {
        $domain = static::getPermissionDomain();
        $permission = config("roles_permissions.permissions.delete_{$domain}");

        return $permission && auth()->user()?->can($permission);
    }
}