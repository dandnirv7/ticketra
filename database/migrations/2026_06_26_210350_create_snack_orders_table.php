<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('snack_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('order_id', 20)->unique();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('booking_id')->nullable()->constrained('bookings')->nullOnDelete();

            $table->string('status', 20)->default('draft')->index();
            $table->decimal('fnb_total', 12, 2)->default(0);
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snack_orders');
    }
};
