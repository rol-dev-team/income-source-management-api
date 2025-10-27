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
        Schema::create('loan_postings', function (Blueprint $table) {
            $table->id();
            $table->enum('transaction_type', ['payment', 'received']);
            $table->enum('head_type', ['bank', 'party'])->nullable();
            $table->unsignedBigInteger('head_id')->nullable();
            $table->unsignedBigInteger('payment_channel_id')->nullable();
            $table->unsignedBigInteger('account_id');
            $table->string('receipt_number')->nullable();
            $table->decimal('amount_bdt', 60, 2);
            $table->date('posting_date');
            $table->text('note')->nullable();
            $table->text('rejected_note')->nullable();
            $table->enum('status', ['approved', 'rejected', 'pending'])
                ->default('pending');
            $table->unsignedBigInteger('loan_id')->nullable();
            $table->unsignedBigInteger('interest_rate_id')->nullable();
            $table->enum('entry_type', ['loan_taken', 'loan_given', 'loan_payment', 'loan_received']);
            $table->timestamps();


            $table->foreign('payment_channel_id')->references('id')->on('payment_channel_details')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('account_numbers')->onDelete('cascade');
            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
            $table->foreign('interest_rate_id')->references('id')->on('loan_interest_rates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_postings');
    }
};
