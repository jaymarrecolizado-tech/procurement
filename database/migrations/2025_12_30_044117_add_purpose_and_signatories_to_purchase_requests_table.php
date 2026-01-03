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
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->text('purpose')->nullable()->after('deficiency_notes');
            $table->string('requested_by_name')->nullable()->after('purpose');
            $table->string('requested_by_designation')->nullable()->after('requested_by_name');
            $table->string('approved_by_name')->nullable()->after('requested_by_designation');
            $table->string('approved_by_designation')->nullable()->after('approved_by_name');
            $table->string('budget_officer_name')->nullable()->after('approved_by_designation');
            $table->string('budget_officer_designation')->nullable()->after('budget_officer_name');
            $table->string('office_address')->nullable()->after('budget_officer_designation');
            $table->string('office_name')->nullable()->after('office_address');
            $table->string('responsibility_center')->nullable()->after('office_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn([
                'purpose',
                'requested_by_name',
                'requested_by_designation',
                'approved_by_name',
                'approved_by_designation',
                'budget_officer_name',
                'budget_officer_designation',
                'office_address',
                'office_name',
                'responsibility_center',
            ]);
        });
    }
};
