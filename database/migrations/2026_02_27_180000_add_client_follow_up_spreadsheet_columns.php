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
        Schema::table('client_follow_up_list', function (Blueprint $table) {
            $table->date('date_of_first_inquiry')->nullable()->after('id');
            $table->string('application', 50)->nullable()->after('date_of_first_inquiry'); // FB, IG, TIKTOK, EMAIL, PHONE, WALK-IN
            $table->string('unit_inquired', 255)->nullable()->after('email');
            $table->string('about_what', 255)->nullable()->after('notes');
            // 1st follow up
            $table->string('sales_exec_1', 100)->nullable()->after('about_what');
            $table->date('date_followed_up_1')->nullable()->after('sales_exec_1');
            $table->string('outcome_1', 255)->nullable()->after('date_followed_up_1');
            $table->text('notes_1')->nullable()->after('outcome_1');
            // 2nd follow up
            $table->string('sales_exec_2', 100)->nullable()->after('notes_1');
            $table->date('date_followed_up_2')->nullable()->after('sales_exec_2');
            $table->string('outcome_2', 255)->nullable()->after('date_followed_up_2');
            $table->text('notes_2')->nullable()->after('outcome_2');
            // 3rd follow up
            $table->string('sales_exec_3', 100)->nullable()->after('notes_2');
            $table->date('date_followed_up_3')->nullable()->after('sales_exec_3');
            $table->string('outcome_3', 255)->nullable()->after('date_followed_up_3');
            $table->text('notes_3')->nullable()->after('outcome_3');
            // 4th follow up
            $table->string('sales_exec_4', 100)->nullable()->after('notes_3');
            $table->date('date_followed_up_4')->nullable()->after('sales_exec_4');
            $table->string('outcome_4', 255)->nullable()->after('date_followed_up_4');
            $table->text('notes_4')->nullable()->after('outcome_4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_follow_up_list', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_first_inquiry', 'application', 'unit_inquired', 'about_what',
                'sales_exec_1', 'date_followed_up_1', 'outcome_1', 'notes_1',
                'sales_exec_2', 'date_followed_up_2', 'outcome_2', 'notes_2',
                'sales_exec_3', 'date_followed_up_3', 'outcome_3', 'notes_3',
                'sales_exec_4', 'date_followed_up_4', 'outcome_4', 'notes_4',
            ]);
        });
    }
};
