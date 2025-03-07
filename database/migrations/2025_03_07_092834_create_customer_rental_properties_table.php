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
        Schema::create('customer_rental_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('rental_property_id');
            $table->foreignId('customer_id');
            $table->decimal('price', 15, 2);
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_rental_properties');
    }
};
