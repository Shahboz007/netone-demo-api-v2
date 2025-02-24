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
        Schema::create('get_money', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('get_money')->cascadeOnUpdate();
            $table->string('name');
            $table->decimal('amount', 12, 2);
            $table->string('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('get_money');
    }
};
