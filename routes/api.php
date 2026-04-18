<?php

use App\Http\Api\Controllers\AuthSyncController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are protected by the Supabase JWT authentication middleware.
| All requests must include a valid JWT token in the Authorization header.
|
| Authorization Header Format:
|   Authorization: Bearer {jwt_token_from_supabase}
|
*/

// Public routes (no authentication required)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
    ]);
});

// Authenticated routes (Supabase JWT required)
Route::middleware('supabase.auth')->group(function () {
    Route::get('/users/me', [UserController::class, 'me']);

    // Update authenticated user's profile
    Route::patch('/users/me', [UserController::class, 'updateProfile']);

    Route::post('/auth/sync', [AuthSyncController::class, 'sync']);

});
