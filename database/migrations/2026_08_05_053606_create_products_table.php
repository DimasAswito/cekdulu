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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique();
            $table->string('name')->nullable();
            $table->string('brand')->nullable();
            $table->string('image_url')->nullable();
            $table->char('nutriscore_grade', 1)->nullable();
            $table->tinyInteger('nova_group')->nullable();
            $table->text('categories')->nullable();
            $table->text('ingredients_text')->nullable();
            $table->json('nutriments')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
