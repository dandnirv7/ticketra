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
        Schema::create('status_kursis', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('kursi_id')->constrained('kursis')->cascadeOnDelete();
            $table->foreignUuid('jadwal_tayang_id')->constrained('jadwal_tayangs')->cascadeOnDelete();

            $table->string('status', 20)->default('tersedia')->index();
            $table->foreignUuid('booking_id')->nullable()->constrained('bookings')->nullOnDelete();

            $table->timestamp('locked_at')->nullable();
            $table->timestamp('lock_expiry')->nullable();

            $table->timestamps();

            $table->unique(['kursi_id', 'jadwal_tayang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_kursis');
    }
};
