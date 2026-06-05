<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_ads', function (Blueprint $table) {
            $table->json('video_links')->nullable()->after('video_link');
            $table->json('social_media_links')->nullable()->after('social_media_post_link');
        });

        DB::table('vehicle_ads')->orderBy('id')->get()->each(function ($ad) {
            $videoLinks = $ad->video_link ? json_encode([$ad->video_link]) : null;
            $socialLinks = $ad->social_media_post_link
                ? json_encode([['channel' => 'Other', 'link' => $ad->social_media_post_link]])
                : null;

            DB::table('vehicle_ads')->where('id', $ad->id)->update([
                'video_links' => $videoLinks,
                'social_media_links' => $socialLinks,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_ads', function (Blueprint $table) {
            $table->dropColumn(['video_links', 'social_media_links']);
        });
    }
};
