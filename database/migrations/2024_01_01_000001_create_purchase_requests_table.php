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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('pr_number')->unique();
            $table->string('project_title');
            $table->text('project_description')->nullable();
            $table->foreignId('end_user_id')->constrained('users')->onDelete('cascade');
            $table->string('end_user_department');
            $table->string('fund_source');
            $table->decimal('estimated_budget', 15, 2);
            $table->enum('urgency_level', ['LOW', 'MEDIUM', 'HIGH', 'URGENT'])->default('MEDIUM');
            $table->string('urgency_timeline')->nullable();
            $table->date('approval_date');
            $table->enum('status', [
                'PR_UNDER_REVIEW', 
                'RFQ_READY', 
                'RFQ_DISSEMINATED', 
                'CANVASS_COMPLETE', 
                'BAC_DOCS_READY', 
                'BAC_APPROVED', 
                'PO_APPROVED', 
                'AWAITING_CONFORME', 
                'PO_COMPLETE', 
                'COA_STAMPED'
            ])->default('PR_UNDER_REVIEW');
            $table->boolean('has_signatures')->default(false);
            $table->boolean('has_specs')->default(false);
            $table->boolean('has_quantity')->default(false);
            $table->boolean('has_market_survey')->default(false);
            $table->text('deficiency_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};