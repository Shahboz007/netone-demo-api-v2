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
        Schema::create('production_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('out_product_id')->constrained('products');
            $table->foreignId('out_amount_type_id')->constrained('amount_types');
            $table->string('name')->unique();
            $table->decimal('out_amount'); // 999 999.00
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_recipes');
    }
};
