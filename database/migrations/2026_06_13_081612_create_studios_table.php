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
        Schema::create('studios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bioskop_id')->constrained('bioskops')->cascadeOnDelete();

            $table->string('nama');
            $table->string('tipe')->default('reguler');
            $table->integer('kapasitas');
            $table->json('layout_kursi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studios');
    }
};
