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
        Schema::create('rental_parties', function (Blueprint $table) {
            $table->id();
            $table->string('party_name');
            $table->string('cell_number')->nullable();
            $table->string('nid')->unique()->nullable();
            $table->string('party_ac_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_parties');
    }
};
