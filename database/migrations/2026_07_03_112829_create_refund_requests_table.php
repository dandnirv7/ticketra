<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('processed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('reason', 500);
            $table->text('admin_notes')->nullable();

            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->string('refund_type', 20)->default('full');

            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};
