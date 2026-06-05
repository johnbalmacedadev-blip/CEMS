<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleStatusDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'showroom',
        'sale_date',
        'sales_price',
        'sale_reservation_amount',
        'sales_person_reserved',
        'sales_person_release',
        'good_sales_review',
        'cash_financing',
        'financing_company',
        'sale_origin',
        'sales_agent_name',
        'agent_cost',
        'finance_revenue_1',
        'finance_revenue_2',
        'sale_status',
        'transfer_cost',
        'release_date',
        'days_from_reservation_to_release',
        'has_insurance',
        'insurance_value',
        'has_trade_in',
        'trade_in_value',
        'days_from_acquisition_to_reservation',
        'customer_first_name',
        'customer_last_name',
        'customer_middle_name',
        'customer_contact_number',
        'customer_date_of_birth',
        'customer_gender',
        'customer_location',
        'customer_purpose',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'release_date' => 'date',
        'customer_date_of_birth' => 'date',
        'sales_price' => 'decimal:2',
        'sale_reservation_amount' => 'decimal:2',
        'agent_cost' => 'decimal:2',
        'finance_revenue_1' => 'decimal:2',
        'finance_revenue_2' => 'decimal:2',
        'transfer_cost' => 'decimal:2',
        'insurance_value' => 'decimal:2',
        'trade_in_value' => 'decimal:2',
        'good_sales_review' => 'boolean',
        'has_insurance' => 'boolean',
        'has_trade_in' => 'boolean',
    ];

    /**
     * Get the vehicle that owns the status detail.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'plate_number', 'plate_number');
    }

    /**
     * Get the formatted sale date
     */
    public function getFormattedSaleDateAttribute()
    {
        return $this->sale_date ? $this->sale_date->format('d-M-y') : null;
    }

    /**
     * Get the formatted release date
     */
    public function getFormattedReleaseDateAttribute()
    {
        return $this->release_date ? $this->release_date->format('d-M-y') : null;
    }

    /**
     * Get the formatted sales price
     */
    public function getFormattedSalesPriceAttribute()
    {
        return $this->sales_price ? '₱' . number_format($this->sales_price, 2) : null;
    }

    /**
     * Get the formatted sale reservation amount
     */
    public function getFormattedSaleReservationAmountAttribute()
    {
        return $this->sale_reservation_amount ? '₱' . number_format($this->sale_reservation_amount, 2) : null;
    }

    /**
     * Get the formatted agent cost
     */
    public function getFormattedAgentCostAttribute()
    {
        return $this->agent_cost ? '₱' . number_format($this->agent_cost, 2) : '₱0.00';
    }

    /**
     * Get the formatted finance revenue 1
     */
    public function getFormattedFinanceRevenue1Attribute()
    {
        return $this->finance_revenue_1 ? '₱' . number_format($this->finance_revenue_1, 2) : null;
    }

    /**
     * Get the formatted finance revenue 2
     */
    public function getFormattedFinanceRevenue2Attribute()
    {
        return $this->finance_revenue_2 ? '₱' . number_format($this->finance_revenue_2, 2) : null;
    }

    /**
     * Get the formatted transfer cost
     */
    public function getFormattedTransferCostAttribute()
    {
        return $this->transfer_cost ? '₱' . number_format($this->transfer_cost, 2) : null;
    }

    /**
     * Calculate days from reservation to release
     */
    public function calculateDaysFromReservationToRelease()
    {
        if ($this->sale_date && $this->release_date) {
            return $this->sale_date->diffInDays($this->release_date);
        }
        return null;
    }

    /**
     * Allowed financing company names (reservation / status details).
     *
     * @return list<string>
     */
    public static function financingCompanyOptions(): array
    {
        return ['Asialink', 'Jaccs', 'Berjaya', 'Orico'];
    }

    /**
     * Sale origin choices (reservation / status details).
     *
     * @return list<string>
     */
    public static function saleOriginOptions(): array
    {
        return ['Agent', 'Audeal', 'Carousell'];
    }
}