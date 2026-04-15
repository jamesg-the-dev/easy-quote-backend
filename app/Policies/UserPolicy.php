<?php

namespace App\Policies;

use App\Models\User;

/**
 * User Policy
 *
 * Defines authorization rules for User model operations.
 * Used with the authorize() helper or $this->authorize() in controllers.
 *
 * Example usage:
 *   $this->authorize('view', $user);          // Can this user view another user?
 *   $this->authorize('update', $user);        // Can this user update another user?
 *   $this->authorize('viewAny', User::class); // Can this user list all users?
 */
class UserPolicy
{
    /**
     * Determine if the user can view any users.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Allow authenticated users to view the user list
        // In production, you might restrict this to admin users
        return true;
    }

    /**
     * Determine if the user can view the user.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function view(User $user, User $targetUser): bool
    {
        // Users can only view their own profile
        // Or allow admins to view anyone
        return $user->id === $targetUser->id || $user->isAdmin();
    }

    /**
     * Determine if the user can create users.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Users are created by Supabase, not directly in Laravel
        // This is typically admin-only or disabled
        return false;
    }

    /**
     * Determine if the user can update the user.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function update(User $user, User $targetUser): bool
    {
        // Users can only update their own profile
        // Or allow admins to update anyone
        return $user->id === $targetUser->id || $user->isAdmin();
    }

    /**
     * Determine if the user can delete the user.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function delete(User $user, User $targetUser): bool
    {
        // Users cannot delete other users
        // Only admins can delete users
        return $user->isAdmin();
    }

    /**
     * Determine if the user can restore the user.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function restore(User $user, User $targetUser): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the user.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function forceDelete(User $user, User $targetUser): bool
    {
        return $user->isAdmin();
    }
}
