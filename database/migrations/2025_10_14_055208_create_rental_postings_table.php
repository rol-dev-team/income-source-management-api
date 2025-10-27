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
        Schema::create('rental_postings', function (Blueprint $table) {
            $table->id();
            $table->enum('transaction_type', ['payment', 'received']);
            $table->unsignedBigInteger('head_id')->nullable();
            $table->unsignedBigInteger('house_id')->nullable();
            $table->unsignedBigInteger('payment_channel_id')->nullable();
            $table->unsignedBigInteger('account_id');
            $table->string('receipt_number')->nullable();
            $table->decimal('amount_bdt', 60, 2);
            $table->date('posting_date');
            $table->text('note')->nullable();
            $table->text('rejected_note')->nullable();
            $table->enum('status', ['approved', 'rejected', 'pending'])
                ->default('pending');
            $table->enum('entry_type', ['rent_received', 'security_money_refund', 'auto_adjustment', 'other_amount', 'security_money_amount']);
            $table->timestamps();


            $table->foreign('payment_channel_id')->references('id')->on('payment_channel_details')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('account_numbers')->onDelete('cascade');
            $table->foreign('head_id')->references('id')->on('rental_parties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_postings');
    }
};
