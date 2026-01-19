<?php

namespace App\Policies;

use App\Models\Attribute;
use App\Models\User;

class AttributePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_attributes');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attribute $attribute): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_attributes');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_attributes');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attribute $attribute): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_attributes');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attribute $attribute): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_attributes');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attribute $attribute): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) || $user->can('manage_attributes');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attribute $attribute): bool
    {
        return $user->hasRole('super_admin');
    }
}
