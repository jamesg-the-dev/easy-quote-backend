<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supabase_user_id')->unique()->index();
            $table->string('email')->index();
            $table->string('full_name');
            $table->string('avatar_url')->nullable();
            $table->timestamps();

            // Add composite index for querying by email and supabase_user_id
            $table->index(['supabase_user_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
