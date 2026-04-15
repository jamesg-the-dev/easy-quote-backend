<?php

namespace App\Services;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use stdClass;

/**
 * Service for verifying and decoding Supabase JWT tokens.
 *
 * This service handles:
 * - Fetching and caching Supabase public keys (JWKS)
 * - Verifying JWT signatures
 * - Validating token claims (issuer, expiry, audience)
 * - Extracting Supabase user ID from token
 */
class SupabaseJwtService
{
    /**
     * Supabase JWKS endpoint URL.
     *
     * @var string
     */
    protected string $jwksUrl;

    /**
     * Supabase project URL (issuer).
     *
     * @var string
     */
    protected string $issuer;

    /**
     * Supabase audience (typically project URL).
     *
     * @var string
     */
    protected string $audience;

    /**
     * Cache key for storing JWKS.
     *
     * @var string
     */
    protected string $cacheKey = 'supabase_jwks';

    /**
     * Cache duration for JWKS (in seconds).
     *
     * @var int
     */
    protected int $cacheDuration = 3600; // 1 hour

    public function __construct()
    {
        $supabaseUrl = rtrim(config('services.supabase.url'), '/');
        $this->jwksUrl = "{$supabaseUrl}/auth/v1/.well-known/jwks.json";
        $this->issuer = "{$supabaseUrl}/auth/v1";
        $this->audience = 'authenticated';
    }

    /**
     * Verify and decode a JWT token.
     *
     * @param string $token The JWT token to verify
     * @return stdClass The decoded token payload
     * @throws Exception If token is invalid or verification fails
     */
    public function verify(string $token): stdClass
    {
        try {
            $keys = $this->getPublicKeys();

            if (empty($keys)) {
                throw new Exception('Unable to fetch Supabase public keys');
            }

            // Decode the JWT header to get the key ID (kid)
            $headerPayload = explode('.', $token);
            if (count($headerPayload) !== 3) {
                throw new Exception('Invalid token format');
            }

            $header = json_decode(
                base64_decode(strtr($headerPayload[0], '-_', '+/'), true)
            );

            if (!isset($header->kid) || !isset($keys[$header->kid])) {
                throw new Exception('Token key ID not found in JWKS');
            }

            // Verify the signature using the correct key
            $key = new Key($keys[$header->kid], 'RS256');
            $decoded = JWT::decode($token, $key);

            // Validate token claims
            $this->validateClaims($decoded);

            return $decoded;
        } catch (SignatureInvalidException $e) {
            Log::warning('JWT signature verification failed: ' . $e->getMessage());
            throw new Exception('Invalid JWT signature', 401);
        } catch (Exception $e) {
            Log::warning('JWT verification error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate JWT token claims.
     *
     * @param stdClass $decoded The decoded token
     * @throws Exception If any claim is invalid
     */
    protected function validateClaims(stdClass $decoded): void
    {
        // Check issuer
        if (!isset($decoded->iss) || $decoded->iss !== $this->issuer) {
            throw new Exception('Invalid token issuer', 401);
        }

        // Check expiration
        if (!isset($decoded->exp) || $decoded->exp < time()) {
            throw new Exception('Token expired', 401);
        }

        // Check subject (must exist)
        if (!isset($decoded->sub)) {
            throw new Exception('Token missing subject claim', 401);
        }
    }

    /**
     * Extract Supabase user ID from token.
     *
     * @param stdClass $decoded The decoded token payload
     * @return string The Supabase user ID (sub claim)
     * @throws Exception If sub claim is missing
     */
    public function getSupabaseUserId(stdClass $decoded): string
    {
        if (!isset($decoded->sub)) {
            throw new Exception('Token missing subject claim (sub)', 401);
        }

        return $decoded->sub;
    }

    /**
     * Get Supabase public keys from JWKS endpoint.
     *
     * Caches the keys for performance.
     *
     * @return array<string, string> Array of [kid => public_key]
     */
    protected function getPublicKeys(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheDuration, function () {
            try {
                $response = file_get_contents($this->jwksUrl);
                if ($response === false) {
                    throw new Exception('Failed to fetch JWKS');
                }

                $jwks = json_decode($response, true);
                if (!isset($jwks['keys'])) {
                    throw new Exception('Invalid JWKS format');
                }

                $keys = [];
                foreach ($jwks['keys'] as $key) {
                    if (isset($key['kid']) && isset($key['x5c']) && !empty($key['x5c'])) {
                        // Convert x5c certificate to PEM format
                        $cert = $key['x5c'][0];
                        $pem = "-----BEGIN CERTIFICATE-----\n" .
                               chunk_split($cert, 64, "\n") .
                               "-----END CERTIFICATE-----\n";
                        $keys[$key['kid']] = $pem;
                    }
                }

                return $keys;
            } catch (Exception $e) {
                Log::error('Failed to fetch or parse JWKS: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Clear the cached JWKS keys.
     *
     * Useful for testing or forcing a refresh.
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }
}
