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
        Schema::create('quotation_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_history_id')->constrained('supplier_quotation_history')->onDelete('cascade');
            $table->string('image_path');
            $table->string('original_filename');
            $table->integer('file_size')->nullable(); // in bytes
            $table->string('mime_type')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('quotation_history_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_images');
    }
};
