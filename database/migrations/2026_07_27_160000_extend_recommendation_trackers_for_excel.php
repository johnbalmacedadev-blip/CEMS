<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recommendation_trackers', function (Blueprint $table) {
            $table->string('plate_number')->nullable()->after('model')->index();
            $table->string('variant')->nullable()->after('plate_number');
            $table->string('transmission')->nullable()->after('variant');
            $table->string('fuel_type')->nullable()->after('transmission');
            $table->string('color')->nullable()->after('fuel_type');
            $table->decimal('purchase_price', 15, 2)->nullable()->after('color');
            $table->string('purchased_from')->nullable()->after('purchase_price');
            $table->date('purchase_date')->nullable()->after('purchased_from');
            $table->string('final_status')->nullable()->after('purchase_date');

            $table->text('paint_recommendation')->nullable()->after('paint');
            $table->text('paint_completion')->nullable()->after('paint_recommendation');
            $table->text('mechanical_recommendation')->nullable();
            $table->text('mechanical_completion')->nullable();
            $table->text('electrical_recommendation')->nullable();
            $table->text('electrical_completion')->nullable();
            $table->text('ecu_cluster_recommendation')->nullable();
            $table->text('ecu_cluster_completion')->nullable();
            $table->text('aircon_recommendation')->nullable();
            $table->text('aircon_completion')->nullable();
            $table->text('interior_recommendation')->nullable();
            $table->text('interior_completion')->nullable();
            $table->text('tires_recommendation')->nullable();
            $table->text('tires_completion')->nullable();
            $table->text('battery_recommendation')->nullable();
            $table->text('battery_completion')->nullable();
            $table->text('misc_recommendation')->nullable();
            $table->text('misc_completion')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('recommendation_trackers', function (Blueprint $table) {
            $table->dropColumn([
                'plate_number', 'variant', 'transmission', 'fuel_type', 'color',
                'purchase_price', 'purchased_from', 'purchase_date', 'final_status',
                'paint_recommendation', 'paint_completion',
                'mechanical_recommendation', 'mechanical_completion',
                'electrical_recommendation', 'electrical_completion',
                'ecu_cluster_recommendation', 'ecu_cluster_completion',
                'aircon_recommendation', 'aircon_completion',
                'interior_recommendation', 'interior_completion',
                'tires_recommendation', 'tires_completion',
                'battery_recommendation', 'battery_completion',
                'misc_recommendation', 'misc_completion',
                'notes',
            ]);
        });
    }
};
