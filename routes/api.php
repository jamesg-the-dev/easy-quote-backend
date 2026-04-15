<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

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
    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    */

    // Get authenticated user's profile
    Route::get('/users/me', [UserController::class, 'me']);

    // Update authenticated user's profile
    Route::patch('/users/me', [UserController::class, 'updateProfile']);

    // User CRUD operations (with authorization via policies)
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Add more authenticated routes here
    |--------------------------------------------------------------------------
    |
    | Example:
    | Route::apiResource('quotes', QuoteController::class);
    | Route::apiResource('projects', ProjectController::class);
    | etc.
    */
});

/*
|--------------------------------------------------------------------------
| Error Responses
|--------------------------------------------------------------------------
|
| All endpoints return JSON responses:
|
| Success (200):
|   {
|     "data": { ... }
|   }
|
| Unauthenticated (401):
|   {
|     "message": "Unauthorized",
|     "error": "..."
|   }
|
| Validation Error (422):
|   {
|     "message": "The given data was invalid.",
|     "errors": { ... }
|   }
|
| Server Error (500):
|   {
|     "message": "Server error"
|   }
*/
