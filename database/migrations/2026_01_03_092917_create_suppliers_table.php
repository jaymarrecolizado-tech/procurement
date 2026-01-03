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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name'); // Normalized name
            $table->string('supplier_name_original'); // Original name as entered
            $table->text('supplier_address')->nullable();
            $table->string('supplier_contact')->nullable();
            $table->string('supplier_email')->nullable();
            $table->string('business_registration_number')->nullable();
            $table->string('supplier_category')->nullable(); // IT, Office Supplies, Construction, etc.
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Index for search performance
            $table->index('supplier_name');
            $table->index('supplier_category');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
