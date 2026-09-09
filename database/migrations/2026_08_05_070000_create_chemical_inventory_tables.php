<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chemical_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('location', 50); // Premium | Annex
            $table->string('item');
            $table->integer('existing_count')->default(0);
            $table->date('count_date')->nullable();
            $table->string('counted_by')->nullable();
            $table->string('location_stored')->nullable();
            $table->string('verified_by_photo')->nullable();
            $table->timestamps();

            $table->index(['location', 'item']);
            $table->index('count_date');
        });

        Schema::create('chemical_movements', function (Blueprint $table) {
            $table->id();
            $table->string('location', 50); // Premium | Annex
            $table->string('period_label', 50)->nullable(); // JULY, AUGUST, etc.
            $table->string('item');
            $table->string('brand')->nullable();
            $table->string('size_or_pieces')->nullable();
            $table->date('movement_date')->nullable();
            $table->string('movement_type', 30)->nullable(); // ADD | REMOVE
            $table->string('quantity_text')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('moved_by')->nullable();
            $table->integer('remaining_count')->nullable();
            $table->timestamps();

            $table->index(['location', 'period_label']);
            $table->index('movement_date');
            $table->index('item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chemical_movements');
        Schema::dropIfExists('chemical_stocks');
    }
};
