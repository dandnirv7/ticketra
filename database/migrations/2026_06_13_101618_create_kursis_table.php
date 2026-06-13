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
        Schema::create('kursis', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('studio_id')->constrained('studios')->cascadeOnDelete();

            $table->string('label_baris', 5);
            $table->integer('nomor_kursi');

            $table->string('tipe_kursi', 20)->default('reguler');

            $table->boolean('is_aktif')->default(true);

            $table->timestamps();

            $table->unique(['studio_id', 'label_baris', 'nomor_kursi']);
            $table->index(['studio_id', 'is_aktif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kursis');
    }
};
