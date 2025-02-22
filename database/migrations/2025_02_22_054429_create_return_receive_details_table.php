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
        Schema::create('return_receive_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_receive_id')->constrained('return_receives');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('amount_type_id')->constrained('amount_types');
            $table->decimal('amount');
            $table->decimal('price', 12, 2);
            $table->decimal('sum_price', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_receive_details');
    }
};
