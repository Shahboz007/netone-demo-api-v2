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
        Schema::create('get_money_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('get_money_id')->constrained('get_money');
            $table->foreignId('user_wallet_id')->constrained('user_wallet');
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('sum_amount', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('get_money_actions');
    }
};
