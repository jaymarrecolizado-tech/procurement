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
            // Add rfq_id for RFQ routing
            $table->foreignId('rfq_id')->nullable()->constrained('rfqs')->onDelete('cascade')->after('purchase_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_routings', function (Blueprint $table) {
            $table->dropForeign(['rfq_id']);
            $table->dropColumn('rfq_id');
        });
    }
};
