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
        Schema::create('rental_property_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_property_id')->constrained('rental_properties');
            $table->foreignId('rental_property_category_id')->constrained('rental_property_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_property_actions');
    }
};
