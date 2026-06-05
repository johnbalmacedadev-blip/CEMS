<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'paint_items',
        'paint_costs',
        'mechanical_electrical_items',
        'mechanical_electrical_costs',
        'cluster_items',
        'cluster_costs',
        'aircon_items',
        'aircon_cost',
        'interior_items',
        'interior_costs',
        'papers_items',
        'papers_costs',
        'tyres_battery_items',
        'tyres_battery_cost',
        'misc_items',
        'misc_costs',
        'total_repair_items',
        'total_repair_cost',
        'post_reservation_repairs',
        'post_reservation_repairs_cost',
        'total_capital_repair_capital_posted',
        'price',
    ];

    protected $casts = [
        'paint_costs' => 'decimal:2',
        'mechanical_electrical_costs' => 'decimal:2',
        'cluster_costs' => 'decimal:2',
        'aircon_cost' => 'decimal:2',
        'interior_costs' => 'decimal:2',
        'papers_costs' => 'decimal:2',
        'tyres_battery_cost' => 'decimal:2',
        'misc_costs' => 'decimal:2',
        'total_repair_cost' => 'decimal:2',
        'post_reservation_repairs_cost' => 'decimal:2',
        'total_capital_repair_capital_posted' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    /**
     * Get the vehicle that owns the expense.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'plate_number', 'plate_number');
    }

    /**
     * Get the formatted paint costs
     */
    public function getFormattedPaintCostsAttribute()
    {
        return '₱' . number_format($this->paint_costs, 2);
    }

    /**
     * Get the formatted mechanical/electrical costs
     */
    public function getFormattedMechanicalElectricalCostsAttribute()
    {
        return '₱' . number_format($this->mechanical_electrical_costs, 2);
    }

    /**
     * Get the formatted cluster costs
     */
    public function getFormattedClusterCostsAttribute()
    {
        return '₱' . number_format($this->cluster_costs, 2);
    }

    /**
     * Get the formatted aircon cost
     */
    public function getFormattedAirconCostAttribute()
    {
        return '₱' . number_format($this->aircon_cost, 2);
    }

    /**
     * Get the formatted interior costs
     */
    public function getFormattedInteriorCostsAttribute()
    {
        return '₱' . number_format($this->interior_costs, 2);
    }

    /**
     * Get the formatted papers costs
     */
    public function getFormattedPapersCostsAttribute()
    {
        return '₱' . number_format($this->papers_costs, 2);
    }

    /**
     * Get the formatted tyres/battery cost
     */
    public function getFormattedTyresBatteryCostAttribute()
    {
        return '₱' . number_format($this->tyres_battery_cost, 2);
    }

    /**
     * Get the formatted misc costs
     */
    public function getFormattedMiscCostsAttribute()
    {
        return '₱' . number_format($this->misc_costs, 2);
    }

    /**
     * Get the formatted total repair cost
     */
    public function getFormattedTotalRepairCostAttribute()
    {
        return '₱' . number_format($this->total_repair_cost, 2);
    }

    /**
     * Get the formatted post reservation repairs cost
     */
    public function getFormattedPostReservationRepairsCostAttribute()
    {
        return '₱' . number_format($this->post_reservation_repairs_cost, 2);
    }

    /**
     * Get the formatted total capital + repair capital posted
     */
    public function getFormattedTotalCapitalRepairCapitalPostedAttribute()
    {
        return '₱' . number_format($this->total_capital_repair_capital_posted, 2);
    }

    /**
     * Get the formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->price, 2);
    }

    /**
     * Calculate total expenses
     */
    public function getTotalExpensesAttribute()
    {
        return $this->paint_costs + 
               $this->mechanical_electrical_costs + 
               $this->cluster_costs + 
               $this->aircon_cost + 
               $this->interior_costs + 
               $this->papers_costs + 
               $this->tyres_battery_cost + 
               $this->misc_costs;
    }

    /**
     * Get formatted total expenses
     */
    public function getFormattedTotalExpensesAttribute()
    {
        return '₱' . number_format($this->total_expenses, 2);
    }
}