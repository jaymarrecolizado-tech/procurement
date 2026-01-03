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
        Schema::create('supplier_quotation_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('rfq_id')->nullable()->constrained('rfqs')->onDelete('set null');
            $table->foreignId('canvass_id')->nullable()->constrained('canvasses')->onDelete('set null');
            $table->foreignId('quotation_id')->nullable()->constrained('supplier_quotations')->onDelete('set null');
            $table->string('item_name');
            $table->string('item_code'); // Auto-generated, normalized
            $table->text('item_description')->nullable();
            $table->decimal('quantity', 15, 2);
            $table->string('unit_of_measure');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->date('quotation_date');
            $table->string('rfq_number')->nullable(); // For reference
            $table->integer('delivery_days')->nullable();
            $table->text('payment_terms')->nullable();
            $table->date('validity_period')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_selected')->default(false);
            $table->foreignId('entered_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes for search performance
            $table->index('supplier_id');
            $table->index('item_code');
            $table->index('item_name');
            $table->index('quotation_date');
            $table->index('rfq_number');
            // Fulltext index with custom name (MySQL has 64 char limit)
            $table->fullText(['item_name', 'item_description', 'item_code'], 'sqh_search_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_quotation_history');
    }
};
