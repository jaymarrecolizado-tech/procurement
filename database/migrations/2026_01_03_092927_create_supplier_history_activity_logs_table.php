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
        Schema::create('supplier_history_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_history_id')->constrained('supplier_quotation_history')->onDelete('cascade');
            $table->enum('action', ['CREATED', 'UPDATED', 'DELETED']);
            $table->string('field_name')->nullable(); // Which field was changed
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('quotation_history_id');
            $table->index('changed_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_history_activity_logs');
    }
};
