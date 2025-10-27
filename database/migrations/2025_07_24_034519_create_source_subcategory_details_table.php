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
        Schema::create('source_subcategory_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('sources')->onDelete('cascade');
            $table->foreignId('source_cat_id')->constrained('source_categories')->onDelete('cascade');
            $table->foreignId('source_subcat_id')->constrained('source_subcategories')->onDelete('cascade');
            $table->foreignId('point_of_contact_id')->constrained('point_of_contacts')->onDelete('cascade');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('source_subcategory_details');
    }
};
