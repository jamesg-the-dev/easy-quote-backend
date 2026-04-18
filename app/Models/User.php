<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $id
 * @property string $email
 * @property string|null $full_name
 * @property string|null $avatar_url
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
#[Fillable(['id', 'email', 'full_name', 'avatar_url'])]
class User extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }


    /**
     * Get the user's full name, or email as fallback.
     *
     * @return Attribute
     */
    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->full_name ?? $this->email,
        );
    }

    /**
     * Check if user is an admin.
     *
     * This is a placeholder. In production, you might:
     * - Store role in database
     * - Check Supabase custom claims
     * - Use a separate roles table
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        // TODO: Implement admin check
        // Example: return $this->role === 'admin';
        return false;
    }
}
