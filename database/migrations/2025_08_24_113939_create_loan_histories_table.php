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
        Schema::create('loan_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('posting_id');

            $table->integer('installment_number')->nullable();
            $table->decimal('amount', 60, 2);
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->integer('term_months');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->foreign('posting_id')
                ->references('id')
                ->on('income_expense_postings')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_histories');
    }
};
