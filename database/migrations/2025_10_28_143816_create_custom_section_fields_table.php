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
        Schema::create('custom_section_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_section_id')->constrained()->onDelete('cascade');
            $table->string('field_name');
            $table->string('field_label');
            $table->enum('field_type', ['text', 'textarea', 'number', 'date', 'email', 'url', 'select', 'checkbox', 'radio']);
            $table->text('field_value')->nullable();
            $table->json('field_options')->nullable(); // For select, radio, checkbox options
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_section_fields');
    }
};
