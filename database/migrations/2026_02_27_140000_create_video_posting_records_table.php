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
        Schema::create('video_posting_records', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('record_date');
            $table->string('type')->default('Video'); // Video, Post, Boost
            $table->string('platform')->nullable();   // e.g. Facebook, YouTube, Instagram
            $table->string('link_url', 500)->nullable();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->string('status')->default('Posted'); // Scheduled, Posted, Pending
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_posting_records');
    }
};
