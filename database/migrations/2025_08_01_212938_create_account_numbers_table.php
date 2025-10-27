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
        Schema::create('account_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_detail_id')->constrained('payment_channel_details')->onDelete('cascade');
            $table->string('ac_no');
            $table->string('ac_name')->nullable();
            $table->text('ac_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_numbers');
    }
};
