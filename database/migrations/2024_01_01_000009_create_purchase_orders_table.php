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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('purchase_request_id')->unique()->constrained('purchase_requests')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade');
            $table->string('supplier_name');
            $table->text('supplier_address')->nullable();
            $table->string('supplier_contact')->nullable();
            $table->decimal('contract_amount', 15, 2);
            $table->text('delivery_instructions')->nullable();
            $table->text('payment_terms')->nullable();
            $table->date('delivery_deadline');
            $table->enum('status', [
                'DRAFT', 
                'PENDING_APPROVAL', 
                'APPROVED', 
                'DISSEMINATED', 
                'AWAITING_CONFORME', 
                'COMPLETE'
            ])->default('DRAFT');
            $table->string('coa_stamp_reference')->nullable();
            $table->date('coa_stamp_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};