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
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('supplier_quotations')->onDelete('cascade');
            $table->foreignId('pr_item_id')->constrained('pr_items')->onDelete('cascade');
            $table->string('item_name');
            $table->text('item_description')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->string('unit_of_measure');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};