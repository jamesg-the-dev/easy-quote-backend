<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
     *
     * @param Request $request
     * @return JsonResponse
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
     * Get all users (admin example).
     *
     * This is an example of how you might list users.
     * In production, you would add authorization checks (policies).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Example: Only allow this if user is admin
        // $this->authorize('viewAny', \App\Models\User::class);

        $users = \App\Models\User::paginate(15);

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    /**
     * Get a specific user by ID.
     *
     * Example request:
     *   GET /api/users/{id}
     *   Authorization: Bearer {jwt_token}
     *
     * @param string $id The user ID (UUID)
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $user = \App\Models\User::findOrFail($id);

        // Example: Add policy check
        // $this->authorize('view', $user);

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
     *
     * @param Request $request
     * @return JsonResponse
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
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $user = \App\Models\User::findOrFail($id);

        // Example: Add authorization check
        // $this->authorize('delete', $user);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
