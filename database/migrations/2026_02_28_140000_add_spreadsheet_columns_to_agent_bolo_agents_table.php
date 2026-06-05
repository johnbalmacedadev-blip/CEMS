<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_bolo_agents', function (Blueprint $table) {
            $table->string('sales_executive')->nullable()->after('name');
            $table->string('signed_bolo')->nullable()->after('email');
            $table->string('one_valid_id')->nullable()->after('signed_bolo');
            $table->date('joined_sales_associate_gc')->nullable()->after('one_valid_id');
            $table->string('facebook_profile_link', 500)->nullable()->after('contact_number');
            $table->string('facebook_page_link', 500)->nullable()->after('facebook_profile_link');
        });
    }

    public function down(): void
    {
        Schema::table('agent_bolo_agents', function (Blueprint $table) {
            $table->dropColumn([
                'sales_executive',
                'signed_bolo',
                'one_valid_id',
                'joined_sales_associate_gc',
                'facebook_profile_link',
                'facebook_page_link',
            ]);
        });
    }
};
