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
        Schema::create('loan_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            $table->integer('installment_no');
            $table->decimal('amount', 60, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'paid', 'unpaid'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_schedules');
    }
};
