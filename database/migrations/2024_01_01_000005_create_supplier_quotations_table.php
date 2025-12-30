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
        Schema::create('supplier_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('canvass_id')->constrained('canvasses')->onDelete('cascade');
            $table->string('supplier_name');
            $table->text('supplier_address')->nullable();
            $table->string('supplier_contact')->nullable();
            $table->string('supplier_email')->nullable();
            $table->date('submitted_date');
            $table->decimal('quote_price', 15, 2);
            $table->integer('delivery_days')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_compliant')->default(false);
            $table->boolean('is_selected')->default(false);
            $table->string('document_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_quotations');
    }
};