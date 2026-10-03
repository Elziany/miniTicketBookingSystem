<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EventFeedback;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventFeedbackPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventFeedback');
    }

    public function view(AuthUser $authUser, EventFeedback $eventFeedback): bool
    {
        return $authUser->can('View:EventFeedback');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventFeedback');
    }

    public function update(AuthUser $authUser, EventFeedback $eventFeedback): bool
    {
        return $authUser->can('Update:EventFeedback');
    }

    public function delete(AuthUser $authUser, EventFeedback $eventFeedback): bool
    {
        return $authUser->can('Delete:EventFeedback');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EventFeedback');
    }

    public function restore(AuthUser $authUser, EventFeedback $eventFeedback): bool
    {
        return $authUser->can('Restore:EventFeedback');
    }

    public function forceDelete(AuthUser $authUser, EventFeedback $eventFeedback): bool
    {
        return $authUser->can('ForceDelete:EventFeedback');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventFeedback');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventFeedback');
    }

    public function replicate(AuthUser $authUser, EventFeedback $eventFeedback): bool
    {
        return $authUser->can('Replicate:EventFeedback');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventFeedback');
    }

}