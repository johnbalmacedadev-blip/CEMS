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
        Schema::create('recommendation_trackers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('year', 20)->nullable();
            $table->string('customer')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('paint')->nullable();
            // Exterior parts checklist
            $table->boolean('hood')->default(false);
            $table->boolean('front_bumper')->default(false);
            $table->boolean('grille')->default(false);
            $table->boolean('fender_right')->default(false);
            $table->boolean('fender_left')->default(false);
            $table->boolean('driver_passenger_door')->default(false);
            $table->boolean('driver_side_door')->default(false);
            $table->boolean('step_board_left')->default(false);
            $table->boolean('step_board_right')->default(false);
            $table->boolean('trunk_lid')->default(false);
            $table->boolean('quarter_panels_left')->default(false);
            $table->boolean('rear_bumper')->default(false);
            $table->boolean('quarter_panel_right')->default(false);
            $table->boolean('passenger_door_right_rear')->default(false);
            $table->boolean('passenger_door_right_front')->default(false);
            $table->boolean('roof')->default(false);
            $table->boolean('spoiler')->default(false);
            $table->boolean('tire_1')->default(false);
            $table->boolean('tire_2')->default(false);
            $table->boolean('tire_3')->default(false);
            $table->boolean('tire_4')->default(false);
            $table->boolean('rims_1')->default(false);
            $table->boolean('rims_2')->default(false);
            $table->boolean('rims_3')->default(false);
            $table->boolean('rims_4')->default(false);
            $table->boolean('front_headlight_1')->default(false);
            $table->boolean('front_headlight_2')->default(false);
            $table->boolean('inner_rear_taillight_1')->default(false);
            $table->boolean('inner_rear_taillight_2')->default(false);
            $table->boolean('taillight_1')->default(false);
            $table->boolean('taillight_2')->default(false);
            $table->boolean('side_mirror_left')->default(false);
            $table->boolean('side_mirror_right')->default(false);
            $table->boolean('mud_guard')->default(false);
            $table->boolean('windshield_front')->default(false);
            $table->boolean('windshield_rear')->default(false);
            // Additional items
            $table->boolean('with_spare_key')->default(false);
            $table->boolean('with_spare_tire')->default(false);
            $table->boolean('with_tools')->default(false);
            $table->boolean('with_matting_complete')->default(false);
            $table->boolean('row_2nd')->default(false);
            $table->boolean('row_3rd')->default(false);
            $table->boolean('row_1st')->default(false);
            $table->boolean('dash_cam')->default(false);
            $table->string('odometers', 100)->nullable();
            $table->string('authorized_drivers')->nullable();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendation_trackers');
    }
};
