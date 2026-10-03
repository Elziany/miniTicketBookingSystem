<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EventSeatPrice;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventSeatPricePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventSeatPrice');
    }

    public function view(AuthUser $authUser, EventSeatPrice $eventSeatPrice): bool
    {
        return $authUser->can('View:EventSeatPrice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventSeatPrice');
    }

    public function update(AuthUser $authUser, EventSeatPrice $eventSeatPrice): bool
    {
        return $authUser->can('Update:EventSeatPrice');
    }

    public function delete(AuthUser $authUser, EventSeatPrice $eventSeatPrice): bool
    {
        return $authUser->can('Delete:EventSeatPrice');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EventSeatPrice');
    }

    public function restore(AuthUser $authUser, EventSeatPrice $eventSeatPrice): bool
    {
        return $authUser->can('Restore:EventSeatPrice');
    }

    public function forceDelete(AuthUser $authUser, EventSeatPrice $eventSeatPrice): bool
    {
        return $authUser->can('ForceDelete:EventSeatPrice');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventSeatPrice');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventSeatPrice');
    }

    public function replicate(AuthUser $authUser, EventSeatPrice $eventSeatPrice): bool
    {
        return $authUser->can('Replicate:EventSeatPrice');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventSeatPrice');
    }

}