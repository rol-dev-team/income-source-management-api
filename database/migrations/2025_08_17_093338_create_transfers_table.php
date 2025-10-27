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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_id');
            $table->enum('transaction_type', ['payment', 'received']);
            $table->unsignedBigInteger('payment_channel_id');
            $table->unsignedBigInteger('account_id');
            $table->decimal('amount_bdt', 60, 2);
            $table->date('transfer_date');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
