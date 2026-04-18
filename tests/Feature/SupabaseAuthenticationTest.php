<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class SupabaseAuthenticationTest extends TestCase
{
    /**
     * Test that unauthenticated request returns 401.
     */
    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->getJson('/api/users/me');

        $response->assertStatus(401)
                 ->assertJsonStructure([
                     'message',
                     'error',
                 ]);
    }

    /**
     * Test that invalid token returns 401.
     */
    public function test_invalid_token_returns_401(): void
    {
        $response = $this->getJson('/api/users/me', [
            'Authorization' => 'Bearer invalid.token.here',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test that malformed authorization header returns 401.
     */
    public function test_malformed_auth_header_returns_401(): void
    {
        $response = $this->getJson('/api/users/me', [
            'Authorization' => 'InvalidFormatToken',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test that user can be created via sync service.
     */
    public function test_user_sync_from_jwt(): void
    {
        $supabaseUserId = 'user-uuid-123';
        $email = 'test@example.com';
        $fullName = 'Test User';

        // Simulate JWT payload
        $jwtPayload = (object) [
            'sub' => $supabaseUserId,
            'email' => $email,
            'user_metadata' => (object) [
                'full_name' => $fullName,
                'avatar_url' => 'https://example.com/avatar.jpg',
            ],
        ];

        // Sync user
        $userSyncService = app(\App\Services\UserSyncService::class);
        $user = $userSyncService->syncFromJwt($jwtPayload);

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'supabase_user_id' => $supabaseUserId,
            'email' => $email,
            'full_name' => $fullName,
        ]);

        $this->assertEquals($supabaseUserId, $user->supabase_user_id);
        $this->assertEquals($email, $user->email);
    }

    /**
     * Test that user sync updates existing user.
     */
    public function test_user_sync_updates_existing_user(): void
    {
        $supabaseUserId = 'user-uuid-456';

        // Create initial user
        User::create([
            'supabase_user_id' => $supabaseUserId,
            'email' => 'old@example.com',
            'full_name' => 'Old Name',
        ]);

        // Simulate updated JWT payload
        $jwtPayload = (object) [
            'sub' => $supabaseUserId,
            'email' => 'new@example.com',
            'user_metadata' => (object) [
                'full_name' => 'New Name',
            ],
        ];

        // Sync user
        $userSyncService = app(\App\Services\UserSyncService::class);
        $user = $userSyncService->syncFromJwt($jwtPayload);

        // Assert user was updated
        $this->assertDatabaseHas('users', [
            'supabase_user_id' => $supabaseUserId,
            'email' => 'new@example.com',
            'full_name' => 'New Name',
        ]);

        $this->assertEquals('new@example.com', $user->email);
        $this->assertEquals('New Name', $user->full_name);
    }

    /**
     * Test finding user by Supabase user ID.
     */
    public function test_find_user_by_supabase_user_id(): void
    {
        $supabaseUserId = 'user-uuid-789';

        $user = User::create([
            'supabase_user_id' => $supabaseUserId,
            'email' => 'test@example.com',
        ]);

        $foundUser = User::findBySupabaseUserId($supabaseUserId);

        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals($supabaseUserId, $foundUser->supabase_user_id);
    }

    /**
     * Test display name accessor.
     */
    public function test_display_name_with_full_name(): void
    {
        $user = User::create([
            'supabase_user_id' => 'user-uuid',
            'email' => 'test@example.com',
            'full_name' => 'John Doe',
        ]);

        $this->assertEquals('John Doe', $user->display_name);
    }

    /**
     * Test display name defaults to email when no full name.
     */
    public function test_display_name_defaults_to_email(): void
    {
        $user = User::create([
            'supabase_user_id' => 'user-uuid',
            'email' => 'test@example.com',
            'full_name' => null,
        ]);

        $this->assertEquals('test@example.com', $user->display_name);
    }

    /**
     * Test user policy - view own profile.
     */
    public function test_user_can_view_own_profile(): void
    {
        $user = User::create([
            'supabase_user_id' => 'user-uuid',
            'email' => 'test@example.com',
        ]);

        $this->assertTrue((new \App\Policies\UserPolicy())->view($user, $user));
    }

    /**
     * Test user policy - cannot view other profiles.
     */
    public function test_user_cannot_view_other_profile(): void
    {
        $user1 = User::create([
            'supabase_user_id' => 'user-uuid-1',
            'email' => 'user1@example.com',
        ]);

        $user2 = User::create([
            'supabase_user_id' => 'user-uuid-2',
            'email' => 'user2@example.com',
        ]);

        $this->assertFalse((new \App\Policies\UserPolicy())->view($user1, $user2));
    }

    /**
     * Test user policy - cannot create users.
     */
    public function test_user_cannot_create_users(): void
    {
        $user = User::create([
            'supabase_user_id' => 'user-uuid',
            'email' => 'test@example.com',
        ]);

        $this->assertFalse((new \App\Policies\UserPolicy())->create($user));
    }

    /**
     * Test user policy - cannot delete users.
     */
    public function test_user_cannot_delete_other_users(): void
    {
        $user1 = User::create([
            'supabase_user_id' => 'user-uuid-1',
            'email' => 'user1@example.com',
        ]);

        $user2 = User::create([
            'supabase_user_id' => 'user-uuid-2',
            'email' => 'user2@example.com',
        ]);

        $this->assertFalse((new \App\Policies\UserPolicy())->delete($user1, $user2));
    }

    /**
     * Test user update validation.
     */
    public function test_user_update_validates_input(): void
    {
        $user = User::create([
            'supabase_user_id' => 'user-uuid',
            'email' => 'test@example.com',
        ]);

        // Test with valid data (would need authenticated request)
        // This is an integration test example

        // Example: assuming we have /api/users/me PATCH endpoint
        // $response = $this->patchJson('/api/users/me', [
        //     'full_name' => 'Updated Name',
        //     'avatar_url' => 'not-a-url',  // Invalid URL
        // ], [
        //     'Authorization' => "Bearer {$validToken}"
        // ]);
        //
        // $response->assertStatus(422)
        //          ->assertJsonValidationErrors('avatar_url');
    }
}
