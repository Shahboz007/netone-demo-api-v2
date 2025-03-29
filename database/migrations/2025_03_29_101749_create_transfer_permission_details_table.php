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
        Schema::create('transfer_permission_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('depart_trans_perm_property')->cascadeOnDelete();
            $table->foreignId('depart_id')->constrained('departs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_permission_details');
    }
};
