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
        Schema::create('quotes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('business_id')->index();
            $table->uuid('created_by_user_id')->nullable()->index();
            $table->bigInteger('customer_id')->nullable()->index();
            $table->string('public_token')->unique();
            $table->string('quote_number')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('subtotal_cents');
            $table->integer('tax_cents');
            $table->integer('total_cents');
            $table->string('currency')->default('AUD');
            $table->enum('status', ['draft', 'sent', 'viewed', 'accepted', 'rejected'])->default('draft');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
