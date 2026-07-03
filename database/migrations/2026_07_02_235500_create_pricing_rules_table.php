<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('day_type')->default('weekend'); // weekend / weekday / holiday
            $table->string('surcharge_type')->default('fixed'); // fixed / percentage
            $table->decimal('surcharge_amount', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
