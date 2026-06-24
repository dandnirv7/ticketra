<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
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
            $table->string('sutradara')->nullable();
            $table->string('penulis')->nullable();
            $table->text('pemain')->nullable();
            $table->string('bahasa')->default('Inggris');
            $table->string('negara')->default('USA');
            $table->string('produksi')->nullable();
            $table->string('rating_usia')->default('13+');
            $table->string('trailer_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
