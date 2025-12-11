<?php

namespace App\Policies;

use App\Models\User;
use App\Models\GroupEquipment;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupEquipmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_group::equipment');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GroupEquipment $groupEquipment): bool
    {
        return $user->can('view_group::equipment');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_group::equipment');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GroupEquipment $groupEquipment): bool
    {
        return $user->can('update_group::equipment');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GroupEquipment $groupEquipment): bool
    {
        return $user->can('delete_group::equipment');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_group::equipment');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, GroupEquipment $groupEquipment): bool
    {
        return $user->can('force_delete_group::equipment');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_group::equipment');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, GroupEquipment $groupEquipment): bool
    {
        return $user->can('restore_group::equipment');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_group::equipment');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, GroupEquipment $groupEquipment): bool
    {
        return $user->can('replicate_group::equipment');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_group::equipment');
    }
}
