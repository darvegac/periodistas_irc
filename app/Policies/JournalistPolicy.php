<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Journalist;
use Illuminate\Auth\Access\HandlesAuthorization;

class JournalistPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_journalist');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Journalist $journalist): bool
    {
        return $user->can('view_journalist');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_journalist');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Journalist $journalist): bool
    {
        return $user->can('update_journalist');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Journalist $journalist): bool
    {
        return $user->can('delete_journalist');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_journalist');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Journalist $journalist): bool
    {
        return $user->can('force_delete_journalist');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_journalist');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Journalist $journalist): bool
    {
        return $user->can('restore_journalist');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_journalist');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Journalist $journalist): bool
    {
        return $user->can('replicate_journalist');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_journalist');
    }
}
