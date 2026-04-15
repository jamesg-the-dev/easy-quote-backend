<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use stdClass;

/**
 * Service for syncing Supabase user data with local database.
 *
 * Handles:
 * - Creating new users from Supabase JWT tokens
 * - Updating existing user information
 * - Maintaining data consistency between Supabase and local DB
 */
class UserSyncService
{
    /**
     * Sync or create a user from Supabase JWT token data.
     *
     * This method:
     * 1. Checks if user exists by supabase_user_id
     * 2. If not exists: Creates new user with provided data
     * 3. If exists: Updates email/name/avatar if changed
     *
     * @param stdClass $jwtPayload The decoded JWT payload from Supabase
     * @return User The synced user instance
     * @throws Exception If user creation/update fails
     */
    public function syncFromJwt(stdClass $jwtPayload): User
    {
        try {
            $supabaseUserId = $jwtPayload->sub;
            $email = $jwtPayload->email ?? null;
            $fullName = $jwtPayload->user_metadata?->full_name ?? null;
            $avatarUrl = $jwtPayload->user_metadata?->avatar_url ?? null;

            // Find existing user or create new instance
            $user = User::findOrNewBySupabaseUserId($supabaseUserId);

            // Determine if this is a new user
            $isNewUser = !$user->exists;

            // Prepare data for sync
            $syncData = [
                'supabase_user_id' => $supabaseUserId,
                'email' => $email,
            ];

            // Only update name/avatar if provided or if creating new user
            if ($fullName !== null) {
                $syncData['full_name'] = $fullName;
            }

            if ($avatarUrl !== null) {
                $syncData['avatar_url'] = $avatarUrl;
            }

            // Update or create user
            $user->fill($syncData)->save();

            if ($isNewUser) {
                Log::info("Created new user from Supabase", [
                    'supabase_user_id' => $supabaseUserId,
                    'email' => $email,
                ]);
            } else {
                Log::debug("Synced existing user from Supabase", [
                    'supabase_user_id' => $supabaseUserId,
                    'email' => $email,
                ]);
            }

            return $user;
        } catch (Exception $e) {
            Log::error('Failed to sync user from JWT: ' . $e->getMessage(), [
                'supabase_user_id' => $jwtPayload->sub ?? 'unknown',
            ]);
            throw $e;
        }
    }

    /**
     * Update user with provided data.
     *
     * Only updates fields that are provided and different from current values.
     *
     * @param User $user The user to update
     * @param array<string, mixed> $data The data to update
     * @return User The updated user instance
     */
    public function updateUser(User $user, array $data): User
    {
        $fillableData = array_intersect_key($data, array_flip($user->getFillable()));

        if (!empty($fillableData)) {
            $user->fill($fillableData)->save();
            Log::debug("Updated user", [
                'user_id' => $user->id,
                'fields' => array_keys($fillableData),
            ]);
        }

        return $user;
    }

    /**
     * Delete a user and associated data.
     *
     * @param User $user The user to delete
     * @return bool Whether the deletion was successful
     */
    public function deleteUser(User $user): bool
    {
        try {
            $userId = $user->id;
            $supabaseUserId = $user->supabase_user_id;

            $result = $user->delete();

            if ($result) {
                Log::info("Deleted user", [
                    'user_id' => $userId,
                    'supabase_user_id' => $supabaseUserId,
                ]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
            throw $e;
        }
    }
}
