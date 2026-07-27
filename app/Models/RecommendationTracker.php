<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendationTracker extends Model
{
    use HasFactory;

    protected $table = 'recommendation_trackers';

    protected $fillable = [
        'date', 'year', 'customer', 'make', 'model', 'paint',
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
        'hood', 'front_bumper', 'grille', 'fender_right', 'fender_left',
        'driver_passenger_door', 'driver_side_door', 'step_board_left', 'step_board_right',
        'trunk_lid', 'quarter_panels_left', 'rear_bumper', 'quarter_panel_right',
        'passenger_door_right_rear', 'passenger_door_right_front', 'roof', 'spoiler',
        'tire_1', 'tire_2', 'tire_3', 'tire_4',
        'rims_1', 'rims_2', 'rims_3', 'rims_4',
        'front_headlight_1', 'front_headlight_2',
        'inner_rear_taillight_1', 'inner_rear_taillight_2',
        'taillight_1', 'taillight_2',
        'side_mirror_left', 'side_mirror_right', 'mud_guard',
        'windshield_front', 'windshield_rear',
        'with_spare_key', 'with_spare_tire', 'with_tools', 'with_matting_complete',
        'row_2nd', 'row_3rd', 'row_1st', 'dash_cam',
        'odometers', 'authorized_drivers', 'vehicle_id',
    ];

    protected $casts = [
        'date' => 'date',
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'hood' => 'boolean',
        'front_bumper' => 'boolean',
        'grille' => 'boolean',
        'fender_right' => 'boolean',
        'fender_left' => 'boolean',
        'driver_passenger_door' => 'boolean',
        'driver_side_door' => 'boolean',
        'step_board_left' => 'boolean',
        'step_board_right' => 'boolean',
        'trunk_lid' => 'boolean',
        'quarter_panels_left' => 'boolean',
        'rear_bumper' => 'boolean',
        'quarter_panel_right' => 'boolean',
        'passenger_door_right_rear' => 'boolean',
        'passenger_door_right_front' => 'boolean',
        'roof' => 'boolean',
        'spoiler' => 'boolean',
        'tire_1' => 'boolean',
        'tire_2' => 'boolean',
        'tire_3' => 'boolean',
        'tire_4' => 'boolean',
        'rims_1' => 'boolean',
        'rims_2' => 'boolean',
        'rims_3' => 'boolean',
        'rims_4' => 'boolean',
        'front_headlight_1' => 'boolean',
        'front_headlight_2' => 'boolean',
        'inner_rear_taillight_1' => 'boolean',
        'inner_rear_taillight_2' => 'boolean',
        'taillight_1' => 'boolean',
        'taillight_2' => 'boolean',
        'side_mirror_left' => 'boolean',
        'side_mirror_right' => 'boolean',
        'mud_guard' => 'boolean',
        'windshield_front' => 'boolean',
        'windshield_rear' => 'boolean',
        'with_spare_key' => 'boolean',
        'with_spare_tire' => 'boolean',
        'with_tools' => 'boolean',
        'with_matting_complete' => 'boolean',
        'row_2nd' => 'boolean',
        'row_3rd' => 'boolean',
        'row_1st' => 'boolean',
        'dash_cam' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function images()
    {
        return $this->hasMany(RecommendationTrackerImage::class)->orderBy('sort_order');
    }

    public function getDisplayTitleAttribute(): string
    {
        $parts = array_filter([$this->year, $this->make, $this->model, $this->plate_number ?: $this->customer]);

        return implode(' – ', $parts) ?: 'Recommendation #'.$this->id;
    }

    public static function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[\s\-]+/', '', (string) $plate) ?? '');
    }

    public static function recommendationCategories(): array
    {
        return [
            'paint' => 'Paint',
            'mechanical' => 'Mechanical',
            'electrical' => 'Electrical',
            'ecu_cluster' => 'ECU / Cluster',
            'aircon' => 'Aircon',
            'interior' => 'Interior',
            'tires' => 'Tires',
            'battery' => 'Battery',
            'misc' => 'Misc',
        ];
    }
}
