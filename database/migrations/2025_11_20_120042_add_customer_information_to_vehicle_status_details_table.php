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
        Schema::table('vehicle_status_details', function (Blueprint $table) {
            $table->string('customer_first_name')->nullable()->after('days_from_reservation_to_release');
            $table->string('customer_last_name')->nullable()->after('customer_first_name');
            $table->string('customer_middle_name')->nullable()->after('customer_last_name');
            $table->string('customer_contact_number')->nullable()->after('customer_middle_name');
            $table->date('customer_date_of_birth')->nullable()->after('customer_contact_number');
            $table->enum('customer_gender', ['Male', 'Female', 'Other'])->nullable()->after('customer_date_of_birth');
            $table->string('customer_location')->nullable()->after('customer_gender');
            $table->text('customer_purpose')->nullable()->after('customer_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_status_details', function (Blueprint $table) {
            $table->dropColumn([
                'customer_first_name',
                'customer_last_name',
                'customer_middle_name',
                'customer_contact_number',
                'customer_date_of_birth',
                'customer_gender',
                'customer_location',
                'customer_purpose',
            ]);
        });
    }
};
