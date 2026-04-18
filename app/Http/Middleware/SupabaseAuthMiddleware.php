<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\SupabaseJwtService;
use App\Services\UserSyncService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Middleware for Supabase JWT authentication.
 */
class SupabaseAuthMiddleware
{
    public function __construct(
        protected SupabaseJwtService $jwtService,
        protected UserSyncService $userSyncService,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): SymfonyResponse  $next
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        try {
            // Extract token from Authorization header
            $token = $this->extractBearerToken($request);

            if (! $token) {
                return $this->unauthorizedResponse('Missing or invalid Authorization header');
            }

            // Verify JWT and get payload
            $jwtPayload = $this->jwtService->verify($token);

            // Sync user from JWT data
            $user = $this->userSyncService->syncFromJwt($jwtPayload);

            // Make user available in request and route model binding
            $request->setUserResolver(fn () => $user);

            return $next($request);
        } catch (\Exception $e) {
            Log::warning('Authentication failed: '.$e->getMessage());

            return $this->unauthorizedResponse($e->getMessage());
        }
    }

    /**
     * Extract Bearer token from Authorization header.
     *
     * @return string|null The token or null if not found
     */
    protected function extractBearerToken(Request $request): ?string
    {
        $header = $request->header('Authorization', '');

        if (preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Return 401 Unauthorized response.
     *
     * @param  string  $message  Error message
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): Response
    {
        return response()->json([
            'message' => 'Unauthorized',
            'error' => $message,
        ], 401);
    }
}
