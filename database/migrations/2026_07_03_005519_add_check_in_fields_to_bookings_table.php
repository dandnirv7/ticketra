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
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('is_checked_in')->default(false)->after('paid_at');
            $table->timestamp('checked_in_at')->nullable()->after('is_checked_in');
            $table->foreignUuid('promo_id')->nullable()->after('checked_in_at')->constrained('promos')->nullOnDelete();
            $table->decimal('discount_amount', 12, 2)->default(0)->after('promo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['promo_id']);
            $table->dropColumn(['is_checked_in', 'checked_in_at', 'promo_id', 'discount_amount']);
        });
    }
};
