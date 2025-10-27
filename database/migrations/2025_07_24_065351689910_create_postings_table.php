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
        Schema::create('postings', function (Blueprint $table) {
            $table->id();
            $table->string('posting_id');
            $table->foreignId('source_id')->constrained('sources')->onDelete('cascade');
            $table->foreignId('transaction_type_id')->constrained('transaction_types')->onDelete('cascade');
            $table->foreignId('source_cat_id')->constrained('source_categories')->onDelete('cascade');
            $table->foreignId('source_subcat_id')->constrained('source_subcategories')->onDelete('cascade');
            $table->foreignId('point_of_contact_id')->constrained('point_of_contacts')->onDelete('cascade');
            $table->foreignId('channel_detail_id')
                ->constrained('payment_channel_details')
                ->onDelete('cascade');
            $table->foreignId('expense_type_id')
                ->nullable()
                ->constrained('expense_types')
                ->onDelete('cascade');
            $table->bigInteger('recived_ac')->nullable();
            $table->bigInteger('from_ac')->nullable();
            $table->bigInteger('foreign_currency')->nullable();
            $table->bigInteger('exchange_rate')->nullable();
            $table->bigInteger('total_amount');
            $table->date('posting_date');
            $table->text('note')->nullable();
            $table->text('rejected_note')->nullable();
            $table->enum('status', ['approved', 'rejected', 'pending'])
                  ->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postings');
    }
};
