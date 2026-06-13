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
        Schema::create('jadwal_tayangs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('film_id')->constrained('films')->restrictOnDelete();
            $table->foreignUuid('studio_id')->constrained('studios')->cascadeOnDelete();

            $table->timestamp('waktu_mulai');
            $table->timestamp('waktu_selesai');
            $table->decimal('harga', 12, 2);

            $table->string('status', 20)->default('terjadwal')->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_tayangs');
    }
};
