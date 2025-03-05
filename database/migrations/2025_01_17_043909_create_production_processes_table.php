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
        Schema::create('production_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_recipe_id')->constrained('production_recipes');
            $table->foreignId('status_id')->constrained('statuses');
            $table->decimal('out_amount')->default(0); // 999 999.00
            $table->decimal('cost_price', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_processes');
    }
};
