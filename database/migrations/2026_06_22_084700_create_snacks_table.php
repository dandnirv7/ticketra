<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('snacks', function (Blueprint $table) {
            $table->string('id')->primary(); 
            $table->string('name');
            $table->text('desc')->nullable();
            $table->integer('price');
            $table->string('emoji');
            $table->string('category');
            $table->string('status')->default('TERSEDIA');
            $table->integer('popular')->default(0);
            $table->string('layout')->default('normal');
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('snacks');
    }
};
