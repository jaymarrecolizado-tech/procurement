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
        Schema::create('bac_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->onDelete('cascade');
            $table->enum('document_type', [
                'ABSTRACT_OF_QUOTATIONS', 
                'PRICE_MATRIX', 
                'TWG_CERT', 
                'RECOMMENDATION', 
                'RESOLUTION'
            ]);
            $table->json('content');
            $table->enum('procurement_mode', [
                'SHOPPING', 
                'SVP', 
                'PUBLIC_BIDDING', 
                'NEGOTIATED', 
                'DIRECT_CONTRACTING'
            ])->nullable();
            $table->enum('status', ['DRAFT', 'PENDING_APPROVAL', 'APPROVED', 'REJECTED'])->default('DRAFT');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bac_documents');
    }
};