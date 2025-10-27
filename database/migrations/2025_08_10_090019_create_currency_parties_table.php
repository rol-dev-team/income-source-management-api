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
        Schema::create('currency_parties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_type_id')->default(2);
            $table->string('party_name');
            $table->string('mobile')->nullable();
            $table->string('nid')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_parties');
    }
};
