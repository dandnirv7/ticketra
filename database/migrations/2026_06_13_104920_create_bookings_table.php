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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('booking_id', 20)->unique();

            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('jadwal_tayang_id')->constrained('jadwal_tayangs')->cascadeOnDelete();

            $table->string('status', 20)->default('draft')->index();
            $table->decimal('total_price', 12, 2)->default(0);
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('fnb_total', 12, 2)->default(0);

            $table->timestamp('locked_at')->nullable();
            $table->timestamp('lock_expiry')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
