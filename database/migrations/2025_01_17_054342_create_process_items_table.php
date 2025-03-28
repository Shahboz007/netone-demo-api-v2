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
        Schema::create('process_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_process_id')->constrained('production_processes')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('amount_type_id')->constrained('amount_types');
            $table->decimal('amount'); // 999 999.00
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_items');
    }
};
