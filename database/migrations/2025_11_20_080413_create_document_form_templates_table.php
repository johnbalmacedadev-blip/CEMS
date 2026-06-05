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
        Schema::create('document_form_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document_type'); // OR, CR, AR, etc.
            $table->json('form_fields'); // Array of field definitions
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['document_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_form_templates');
    }
};
