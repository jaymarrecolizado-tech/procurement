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
        // Add indexes to frequently queried columns for better performance
        // Using try-catch to handle cases where indexes already exist
        
        // Purchase Requests indexes
        try {
            Schema::table('purchase_requests', function (Blueprint $table) {
                $table->index('status');
                $table->index('end_user_id');
                $table->index('created_at');
            });
        } catch (\Exception $e) {
            // Index may already exist, continue
        }

        // RFQs indexes
        try {
            Schema::table('rfqs', function (Blueprint $table) {
                $table->index('status');
                $table->index('procurement_officer_id');
                $table->index('created_at');
            });
        } catch (\Exception $e) {
            // Index may already exist, continue
        }

        // Approval Routings indexes
        try {
            Schema::table('approval_routings', function (Blueprint $table) {
                $table->index('approver_id');
                $table->index('status');
                $table->index('document_type');
                $table->index('sequence');
                // Composite index for common query pattern
                $table->index(['approver_id', 'status']);
            });
        } catch (\Exception $e) {
            // Index may already exist, continue
        }

        // Canvasses indexes
        try {
            Schema::table('canvasses', function (Blueprint $table) {
                $table->index('canvasser_id');
                $table->index('status');
                $table->index('deadline');
            });
        } catch (\Exception $e) {
            // Index may already exist, continue
        }

        // Purchase Orders indexes
        try {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->index('status');
                $table->index('created_at');
            });
        } catch (\Exception $e) {
            // Index may already exist, continue
        }

        // BAC Documents indexes
        try {
            Schema::table('bac_documents', function (Blueprint $table) {
                $table->index('status');
                $table->index('document_type');
            });
        } catch (\Exception $e) {
            // Index may already exist, continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['end_user_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('rfqs', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['procurement_officer_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('approval_routings', function (Blueprint $table) {
            $table->dropIndex(['approver_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['document_type']);
            $table->dropIndex(['sequence']);
            $table->dropIndex(['approver_id', 'status']);
        });

        Schema::table('canvasses', function (Blueprint $table) {
            $table->dropIndex(['canvasser_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['deadline']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('bac_documents', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['document_type']);
        });
    }

};

