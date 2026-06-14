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
        Schema::create('payment_webhook', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('webhook_id', 100)->unique();

            $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();

            $table->string('transaction_id', 100)->index();
            $table->string('status', 20);
            $table->string('payment_type', 50)->nullable();
            $table->json('payload');

            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_webhook');
    }
};
