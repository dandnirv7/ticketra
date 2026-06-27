<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('snack_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('snack_order_id')->constrained('snack_orders')->cascadeOnDelete();
            $table->string('snack_id');
            $table->string('snack_name');
            $table->string('snack_emoji');
            $table->integer('qty');
            $table->integer('price');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snack_order_items');
    }
};
