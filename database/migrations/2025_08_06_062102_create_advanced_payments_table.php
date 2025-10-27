<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('advanced_payments', function (Blueprint $table) {
            $table->id();
            $table->string('advanced_payment_type');
            $table->unsignedBigInteger('sub_cat_id');
            $table->unsignedBigInteger('point_of_contact_id');
            $table->decimal('amount', 12, 2);
            $table->decimal('auto_adjustment_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('advanced_payments');
    }
};

