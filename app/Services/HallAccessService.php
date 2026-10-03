<?php

namespace App\Services;

use App\Enum\UserType;
use App\Models\Hall;
use App\Models\User;
use Illuminate\Support\Collection;

class HallAccessService
{
    public function hasGlobalPermission(User $user, string $permission): bool
    {
        return $user->can('Global:'.$permission);
    }

    public function allows(User $user, string $permission, ?int $hallId = null): bool
    {
        if (! $user->can($permission)) {
            return false;
        }

        if ($this->hasGlobalPermission($user, $permission) || $user->type === UserType::ADMIN) {
            return true;
        }

        if ($hallId === null) {
            return false;
        }

        return $this->accessibleHallIds($user)->contains($hallId);
    }

    /**
     * Halls assigned to the user or to anyone below them in the manager tree.
     *
     * @return Collection<int, int>
     */
    public function accessibleHallIds(User $user): Collection
    {
        if ($user->type === UserType::ADMIN || $this->hasGlobalPermission($user, 'ViewAny:Hall')) {
            return Hall::query()->pluck('id');
        }

        $userIds = $this->descendantIds($user);
        $userIds[] = $user->id;

        return Hall::query()
            ->whereHas('agents', function ($query) use ($userIds) {
                $query->whereIn('users.id', $userIds);
            })
            ->pluck('id');
    }

    /**
     * Unlimited-depth reports. Peers (same manager) are not included.
     *
     * @return list<int>
     */
    public function descendantIds(User $user): array
    {
        $ids = [];
        $frontier = User::query()->where('manager_id', $user->id)->pluck('id')->all();

        while ($frontier !== []) {
            $ids = array_merge($ids, $frontier);
            $frontier = User::query()->whereIn('manager_id', $frontier)->pluck('id')->all();
        }

        return $ids;
    }

    public function managesUser(User $actor, User $subject): bool
    {
        if ($actor->id === $subject->id) {
            return true;
        }

        if ($this->hasGlobalPermission($actor, 'View:User') || $actor->type === UserType::ADMIN) {
            return true;
        }

        return in_array($subject->id, $this->descendantIds($actor), true);
    }
}
