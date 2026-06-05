<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'make_id',
        'model_id',
        'make', // Keep for backward compatibility
        'model', // Keep for backward compatibility
        'variant',
        'body_type',
        'transmission',
        'fuel_type',
        'kilometers',
        'plate_number',
        'colour',
        'with_tools',
        'with_matting',
        'with_spare_tire',
        'purchase_price',
        'posted_price',
        'sold_price',
        'option1_cash_out',
        'option1_12mos',
        'option1_24mos',
        'option1_36mos',
        'option1_48mos',
        'option2_cash_out',
        'option2_12mos',
        'option2_24mos',
        'option2_36mos',
        'option2_48mos',
        'purchased_from',
        'purchase_date',
        'spare_key',
        'notes',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'posted_price' => 'decimal:2',
        'sold_price' => 'decimal:2',
        'option1_cash_out' => 'decimal:2',
        'option1_12mos' => 'decimal:2',
        'option1_24mos' => 'decimal:2',
        'option1_36mos' => 'decimal:2',
        'option1_48mos' => 'decimal:2',
        'option2_cash_out' => 'decimal:2',
        'option2_12mos' => 'decimal:2',
        'option2_24mos' => 'decimal:2',
        'option2_36mos' => 'decimal:2',
        'option2_48mos' => 'decimal:2',
        'with_tools' => 'boolean',
        'with_matting' => 'boolean',
        'with_spare_tire' => 'boolean',
        'spare_key' => 'boolean',
    ];

    /**
     * Get the formatted purchase date
     */
    public function getFormattedPurchaseDateAttribute()
    {
        return $this->purchase_date->format('d-M-y');
    }

    /**
     * Get the formatted purchase price
     */
    public function getFormattedPurchasePriceAttribute()
    {
        return '₱' . number_format($this->purchase_price, 2);
    }


    /**
     * Scope for available vehicles
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'Available');
    }

    /**
     * Scope for under maintenance vehicles
     */
    public function scopeUnderMaintenance($query)
    {
        return $query->where('status', 'Under Maintenance');
    }

    /**
     * Scope for reserved vehicles
     */
    public function scopeReserved($query)
    {
        return $query->where('status', 'Reserved');
    }

    /**
     * Scope for released vehicles
     */
    public function scopeReleased($query)
    {
        return $query->where('status', 'Released');
    }

    /**
     * Get the make that owns the vehicle.
     */
    public function make()
    {
        return $this->belongsTo(Make::class);
    }

    /**
     * Get the model that owns the vehicle.
     */
    public function vehicleModel()
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }

    /**
     * Get the images for the vehicle.
     */
    public function images()
    {
        return $this->hasMany(VehicleImage::class)->ordered();
    }

    /**
     * Get the primary image for the vehicle.
     */
    public function primaryImage()
    {
        return $this->hasOne(VehicleImage::class)->where('is_primary', true);
    }

    /**
     * Get the expense record for the vehicle.
     */
    public function expense()
    {
        return $this->hasOne(VehicleExpense::class, 'plate_number', 'plate_number');
    }

    /**
     * Get the status detail record for the vehicle.
     */
    public function statusDetail()
    {
        return $this->hasOne(VehicleStatusDetail::class, 'plate_number', 'plate_number');
    }

    /**
     * Get the gas expenses for the vehicle.
     */
    public function gasExpenses()
    {
        return $this->hasMany(GasExpense::class, 'plate_number', 'plate_number');
    }

    /**
     * Get the incentive record for the vehicle.
     */
    public function incentive()
    {
        return $this->hasOne(VehicleIncentive::class);
    }

    /**
     * Get the custom sections for the vehicle.
     */
    public function customSections()
    {
        return $this->hasMany(CustomSection::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get the custom fields for the vehicle.
     */
    public function customFields()
    {
        return $this->hasMany(VehicleCustomField::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get the expense items for the vehicle.
     */
    public function expenseItems()
    {
        return $this->hasMany(ExpenseItem::class)
            ->where('payment_tag', 'Vehicle')
            ->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the ads for the vehicle.
     */
    public function ads()
    {
        return $this->hasMany(VehicleAd::class)->orderBy('posted_date', 'desc');
    }

    /**
     * Get the forfeit details for the vehicle.
     */
    public function forfeitDetails()
    {
        return $this->hasMany(VehicleForfeitDetail::class)->orderBy('forfeit_date', 'desc');
    }

    /**
     * Get the follow-up documents for this vehicle.
     */
    public function followUpDocuments()
    {
        return $this->hasMany(FollowUpDocument::class)->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')->orderBy('due_date')->orderBy('created_at', 'desc');
    }

    /**
     * Get the sales agent commission records linked to this vehicle.
     */
    public function salesAgentCommissions()
    {
        return $this->hasMany(SalesAgentCommission::class)->orderBy('date_sent', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Get the transfer OR/CR records for this vehicle.
     */
    public function transferOrcrs()
    {
        return $this->hasMany(TransferOrcr::class)->orderBy('date', 'desc')->orderBy('release_date', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Get the video/posting tracker records for this vehicle.
     */
    public function videoPostingRecords()
    {
        return $this->hasMany(VideoPostingRecord::class)->orderBy('record_date', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Get the buffing records for this vehicle.
     */
    public function buffingRecords()
    {
        return $this->hasMany(BuffingRecord::class)->orderBy('buffing_date', 'desc')->orderBy('created_at', 'desc');
    }
    
    /**
     * Get all expense items for the vehicle (including from transactions with mixed items).
     * This includes items from transactions that have at least one item for this vehicle.
     */
    public function allExpenseItems()
    {
        // Get all transaction IDs that have at least one item for this vehicle
        $transactionIds = ExpenseItem::where('vehicle_id', $this->id)
            ->where('payment_tag', 'Vehicle')
            ->pluck('expense_transaction_id')
            ->unique();
        
        // Get all items from those transactions that are for this vehicle
        return ExpenseItem::whereIn('expense_transaction_id', $transactionIds)
            ->where('vehicle_id', $this->id)
            ->where('payment_tag', 'Vehicle')
            ->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the documents for the vehicle.
     */
    public function documents()
    {
        return $this->hasMany(VehicleDocument::class);
    }

    /**
     * Get acquisition documents for the vehicle.
     */
    public function acquisitionDocuments()
    {
        return $this->hasMany(VehicleDocument::class)->where('process_type', 'ACQUISITION');
    }

    public function reservationDocuments()
    {
        return $this->hasMany(VehicleDocument::class)->where('process_type', 'RESERVATION');
    }

    public function releaseDocuments()
    {
        return $this->hasMany(VehicleDocument::class)->where('process_type', 'RELEASE');
    }

    /**
     * Get custom fields for a specific section.
     */
    public function customFieldsForSection($sectionName)
    {
        return $this->customFields()->where('section_name', $sectionName);
    }

    /**
     * Get the full vehicle name with make and model
     */
    public function getFullNameAttribute()
    {
        $name = $this->year;
        
        // Get make name
        $makeName = '';
        if (is_string($this->make)) {
            // Old string column
            $makeName = $this->make;
        } elseif (is_object($this->make) && isset($this->make->name)) {
            // Relationship object
            $makeName = $this->make->name;
        }
        
        // Get model name
        $modelName = '';
        if (is_string($this->model)) {
            // Old string column
            $modelName = $this->model;
        } elseif (is_object($this->vehicleModel) && isset($this->vehicleModel->name)) {
            // Relationship object
            $modelName = $this->vehicleModel->name;
        }
        
        $name .= ' ' . $makeName . ' ' . $modelName;
        
        if ($this->variant) {
            $name .= ' ' . $this->variant;
        }
        
        return trim($name);
    }
}