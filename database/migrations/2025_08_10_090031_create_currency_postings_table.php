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
        Schema::create('currency_postings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_type_id')->default(2);
            $table->enum('transaction_type', ['buy', 'sell','payment', 'received']);
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->unsignedBigInteger('currency_party_id');
            $table->unsignedBigInteger('payment_channel_id');
            $table->unsignedBigInteger('account_id');
            $table->string('party_account_number')->nullable();
            $table->decimal('currency_amount', 60, 2)->nullable();
            $table->decimal('exchange_rate', 60, 4)->nullable();
            $table->decimal('amount_bdt', 60, 2);
            $table->date('posting_date'); 
            $table->text('note')->nullable();
            $table->text('rejected_note')->nullable();
            $table->enum('status', ['approved', 'rejected', 'pending'])
                  ->default('pending');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->foreign('currency_party_id')->references('id')->on('currency_parties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_postings');
    }
};
