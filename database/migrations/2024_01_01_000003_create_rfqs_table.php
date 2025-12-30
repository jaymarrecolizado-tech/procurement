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
        Schema::create('rfqs', function (Blueprint $table) {
            $table->id();
            $table->string('rfq_number')->unique();
            $table->foreignId('purchase_request_id')->unique()->constrained('purchase_requests')->onDelete('cascade');
            $table->foreignId('procurement_officer_id')->constrained('users')->onDelete('cascade');
            $table->text('delivery_schedule')->nullable();
            $table->text('payment_terms')->nullable();
            $table->date('canvassing_deadline');
            $table->enum('status', ['PENDING', 'ACTIVE', 'COMPLETED'])->default('PENDING');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfqs');
    }
};