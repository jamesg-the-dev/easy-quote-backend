<?php

namespace App\Http\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthSyncController extends Controller
{
    /**
     * Sync Supabase authenticated user into local database.
     * This endpoint should be called after login/signup or on first API request.
     */
    public function sync(Request $request)
    {
        $supabaseUser = $request->auth_user;

        if (! $supabaseUser || ! isset($supabaseUser->sub)) {
            return response()->json([
                'message' => 'Invalid Supabase user payload',
            ], 401);
        }

        $user = User::updateOrCreate(
            [
                'id' => $supabaseUser->sub, // Supabase auth.users.id (UUID)
            ],
            [
                'email' => $supabaseUser->email ?? null,
                'full_name' => $this->extractFullName($supabaseUser),
                'avatar_url' => '',
            ]
        );

        return response()->json([
            'user' => $user,
        ]);
    }

    private function extractFullName($supabaseUser): ?string
    {
        return $supabaseUser->user_metadata->full_name
            ?? $supabaseUser->user_metadata->name
            ?? null;
    }
}
