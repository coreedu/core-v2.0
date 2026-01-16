<?php

namespace App\Policies\Time;

use App\Models\User;
use App\Models\Time\Context;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContextPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_context');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Context $context): bool
    {
        return $user->can('view_context');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_context');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Context $context): bool
    {
        return $user->can('update_context');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Context $context): bool
    {
        return $user->can('delete_context');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_context');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Context $context): bool
    {
        return $user->can('force_delete_context');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_context');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Context $context): bool
    {
        return $user->can('restore_context');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_context');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Context $context): bool
    {
        return $user->can('replicate_context');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_context');
    }
}
