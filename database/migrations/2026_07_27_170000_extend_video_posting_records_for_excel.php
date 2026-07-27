<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('video_posting_records', function (Blueprint $table) {
            $table->string('vlogger')->nullable()->after('title')->index();
            $table->string('category')->nullable()->after('vlogger')->index();
            $table->string('showroom')->nullable()->after('category')->index();
            $table->string('featured_car_or_client')->nullable()->after('showroom');
            $table->string('plate_number')->nullable()->after('featured_car_or_client')->index();
            $table->string('active_unit')->nullable()->after('plate_number');
            $table->date('date_uploaded_gdrive')->nullable()->after('active_unit');
            $table->date('date_posted_social')->nullable()->after('date_uploaded_gdrive');
            $table->string('gdrive_file_name')->nullable()->after('date_posted_social');
            $table->string('source_sheet')->nullable()->after('gdrive_file_name');
        });
    }

    public function down(): void
    {
        Schema::table('video_posting_records', function (Blueprint $table) {
            $table->dropColumn([
                'vlogger',
                'category',
                'showroom',
                'featured_car_or_client',
                'plate_number',
                'active_unit',
                'date_uploaded_gdrive',
                'date_posted_social',
                'gdrive_file_name',
                'source_sheet',
            ]);
        });
    }
};
