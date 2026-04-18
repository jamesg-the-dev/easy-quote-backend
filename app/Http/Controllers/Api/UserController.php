<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Example API controller demonstrating authenticated user access.
 *
 * This controller is protected by the SupabaseAuthMiddleware,
 * ensuring all requests are authenticated via Supabase JWT.
 *
 * Usage in routes:
 *   Route::middleware('supabase.auth')->group(function () {
 *       Route::apiResource('users', UserController::class);
 *   });
 */
class UserController extends Controller
{
    /**
     * Get the authenticated user's profile.
     *
     * Example request:
     *   GET /api/users/me
     *   Authorization: Bearer {jwt_token}
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'supabase_user_id' => $user->supabase_user_id,
                'email' => $user->email,
                'full_name' => $user->full_name,
                'avatar_url' => $user->avatar_url,
                'display_name' => $user->display_name,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    /**
     * Update the authenticated user's profile.
     *
     * Example request:
     *   PATCH /api/users/me
     *   Authorization: Bearer {jwt_token}
     *   Content-Type: application/json
     *
     *   {
     *     "full_name": "John Doe",
     *     "avatar_url": "https://example.com/avatar.jpg"
     *   }
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'avatar_url' => 'sometimes|nullable|url|max:2048',
        ]);

        $user = $request->user();
        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'supabase_user_id' => $user->supabase_user_id,
                'email' => $user->email,
                'full_name' => $user->full_name,
                'avatar_url' => $user->avatar_url,
                'display_name' => $user->display_name,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    /**
     * Delete a user (admin only example).
     *
     * Example request:
     *   DELETE /api/users/{id}
     *   Authorization: Bearer {jwt_token}
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Example: Add authorization check
        // $this->authorize('delete', $user);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
