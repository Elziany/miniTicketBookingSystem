<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\FeedbackQuestion;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeedbackQuestionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FeedbackQuestion');
    }

    public function view(AuthUser $authUser, FeedbackQuestion $feedbackQuestion): bool
    {
        return $authUser->can('View:FeedbackQuestion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FeedbackQuestion');
    }

    public function update(AuthUser $authUser, FeedbackQuestion $feedbackQuestion): bool
    {
        return $authUser->can('Update:FeedbackQuestion');
    }

    public function delete(AuthUser $authUser, FeedbackQuestion $feedbackQuestion): bool
    {
        return $authUser->can('Delete:FeedbackQuestion');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FeedbackQuestion');
    }

    public function restore(AuthUser $authUser, FeedbackQuestion $feedbackQuestion): bool
    {
        return $authUser->can('Restore:FeedbackQuestion');
    }

    public function forceDelete(AuthUser $authUser, FeedbackQuestion $feedbackQuestion): bool
    {
        return $authUser->can('ForceDelete:FeedbackQuestion');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FeedbackQuestion');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FeedbackQuestion');
    }

    public function replicate(AuthUser $authUser, FeedbackQuestion $feedbackQuestion): bool
    {
        return $authUser->can('Replicate:FeedbackQuestion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FeedbackQuestion');
    }

}