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
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', [
                'OR',
                'CR',
                'AR',
                'IDS',
                'PROMISSORY',
                'CHATTEL',
                'REGISTRY_OF_DEEDS',
                'SEC_CERT',
                'DEED_OF_SALE',
                'VOLUNTARY_SURRENDER',
                'SHERRIF_LETTER',
                'DEED_OF_SALE_BANK'
            ]);
            $table->enum('process_type', ['ACQUISITION', 'RESERVATION', 'RELEASE'])->default('ACQUISITION');
            $table->enum('storage_type', ['file', 'link', 'form'])->default('form');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->text('file_link')->nullable();
            $table->json('form_data')->nullable(); // Store custom form data as JSON
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure one document per type per vehicle per process
            $table->unique(['vehicle_id', 'document_type', 'process_type'], 'unique_vehicle_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_documents');
    }
};
