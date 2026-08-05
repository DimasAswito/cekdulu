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
        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condition_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('allergen_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('nutrient_key')->nullable();
            $table->string('operator')->nullable();
            $table->decimal('threshold', 8, 2)->nullable();
            $table->string('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_rules');
    }
};
