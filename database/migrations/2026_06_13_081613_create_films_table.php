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
        Schema::create('films', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul');
            $table->string('poster_url')->nullable();
            $table->text('sinopsis')->nullable();
            $table->integer('durasi_menit');
            $table->decimal('rating', 2, 1)->nullable();
            $table->string('genre')->nullable();
            $table->date('tanggal_rilis')->nullable();
            $table->boolean('sedang_tayang')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
