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
        Schema::create('vehicle_document_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_document_id')->constrained('vehicle_documents')->onDelete('cascade');
            $table->enum('type', ['file', 'link']); // file or link
            $table->string('file_path')->nullable(); // For file uploads
            $table->string('file_name')->nullable(); // Original file name
            $table->text('file_link')->nullable(); // For external links
            $table->integer('sort_order')->default(0); // For ordering
            $table->timestamps();
            
            $table->index(['vehicle_document_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_document_files');
    }
};
