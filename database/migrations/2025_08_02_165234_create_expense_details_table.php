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
        Schema::create('expense_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posting_id')
                ->nullable()
                ->constrained('postings')
                ->onDelete('cascade');
            $table->foreignId('channel_detail_id')
                ->nullable()
                ->constrained('payment_channel_details')
                ->onDelete('cascade');
            $table->string('recived_ac')->nullable();
            $table->string('from_ac')->nullable();
            $table->decimal('amount', 65, 2)->nullable();
            $table->decimal('exchange_rate', 65, 4)->nullable();
            $table->date('expense_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_details');
    }
};
