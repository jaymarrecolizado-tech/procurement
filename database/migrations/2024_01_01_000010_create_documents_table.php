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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->nullable()->constrained('purchase_requests')->onDelete('cascade');
            $table->foreignId('rfq_id')->nullable()->constrained('rfqs')->onDelete('cascade');
            $table->foreignId('po_id')->nullable()->constrained('purchase_orders')->onDelete('cascade');
            $table->enum('document_type', [
                'PR', 
                'RFQ', 
                'QUOTATION', 
                'AOQ', 
                'BAC_RESOLUTION', 
                'PO', 
                'CONFORME', 
                'COA_PACKET'
            ]);
            $table->string('file_name');
            $table->string('file_path');
            $table->integer('file_size');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};