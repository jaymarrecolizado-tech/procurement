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
        Schema::table('approval_routings', function (Blueprint $table) {
            // Make bac_document_id nullable to support PR routing
            $table->foreignId('bac_document_id')->nullable()->change();
            
            // Add purchase_request_id for PR routing
            $table->foreignId('purchase_request_id')->nullable()->constrained('purchase_requests')->onDelete('cascade')->after('bac_document_id');
            
            // Add document_type to distinguish between PR, RFQ, BAC
            $table->string('document_type')->default('BAC')->after('purchase_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_routings', function (Blueprint $table) {
            $table->dropForeign(['purchase_request_id']);
            $table->dropColumn(['purchase_request_id', 'document_type']);
            $table->foreignId('bac_document_id')->nullable(false)->change();
        });
    }
};
