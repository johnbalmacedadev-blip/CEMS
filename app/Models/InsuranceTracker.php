<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceTracker extends Model
{
    use HasFactory;

    protected $table = 'insurance_tracker';

    protected $fillable = [
        'showroom',
        'sales',
        'year',
        'make',
        'model',
        'number',
        'vehicle_id',
        'transaction',
        'source',
        'reservation_date',
        'release_date',
        'amount',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'release_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getDisplayYearAttribute(): string
    {
        if ($this->vehicle_id && $this->vehicle) {
            return (string) $this->vehicle->year;
        }
        return $this->year ?? '—';
    }

    public function getDisplayMakeAttribute(): string
    {
        if ($this->vehicle_id && $this->vehicle) {
            $make = $this->vehicle->make;
            if (is_string($make)) {
                return $make;
            }
            if (is_object($make) && isset($make->name)) {
                return $make->name;
            }
        }
        return $this->make ?? '—';
    }

    public function getDisplayModelAttribute(): string
    {
        if ($this->vehicle_id && $this->vehicle) {
            if (is_string($this->vehicle->model ?? null)) {
                return $this->vehicle->model;
            }
            if ($this->vehicle->vehicleModel && isset($this->vehicle->vehicleModel->name)) {
                return $this->vehicle->vehicleModel->name;
            }
        }
        return $this->model ?? '—';
    }

    public function getDisplayNumberAttribute(): string
    {
        if ($this->vehicle_id && $this->vehicle) {
            return $this->vehicle->plate_number ?? '—';
        }
        return $this->number ?? '—';
    }
}
