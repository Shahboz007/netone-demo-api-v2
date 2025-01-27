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
        Schema::create('amount_setting', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_from_id')->constrained('amount_types');
            $table->float('amount_from');
            $table->foreignId('type_to_id')->constrained('amount_types');
            $table->float('amount_to');
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amount_setting');
    }
};
