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
        Schema::create('payment_channel_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('payment_channels')->onDelete('cascade');
            $table->string('method_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_channel_details');
    }
};
