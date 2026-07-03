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
        Schema::create('bioskop_snack', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('bioskop_id')->constrained('bioskops')->cascadeOnDelete();
            $table->string('snack_id');
            $table->foreign('snack_id')->references('id')->on('snacks')->cascadeOnDelete();
            $table->timestamps();

            // Unique constraint to prevent duplicate association
            $table->unique(['bioskop_id', 'snack_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bioskop_snack');
    }
};
