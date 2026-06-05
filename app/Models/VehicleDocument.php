<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'document_type',
        'process_type',
        'storage_type',
        'file_path',
        'file_name',
        'file_link',
        'form_data',
        'notes',
        'is_completed',
        'check_date',
        'checked_by',
    ];

    protected $casts = [
        'form_data' => 'array',
        'is_completed' => 'boolean',
        'check_date' => 'date',
    ];

    /**
     * Get the vehicle that owns the document.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get all files and links for this document.
     */
    public function files()
    {
        return $this->hasMany(VehicleDocumentFile::class)->orderBy('sort_order');
    }

    /**
     * Get the document type label.
     */
    public function getDocumentTypeLabelAttribute()
    {
        $labels = [
            'OR' => 'OR',
            'CR' => 'CR',
            'AR' => 'AR',
            'IDS' => 'IDS',
            'PROMISSORY' => 'PROMISSORY',
            'CHATTEL' => 'CHATTEL',
            'REGISTRY_OF_DEEDS' => 'REGISTRY OF DEEDS',
            'SEC_CERT' => 'SEC CERT',
            'DEED_OF_SALE' => 'DEED OF SALE',
            'VOLUNTARY_SURRENDER' => 'VOLUNTARY SURRENDER',
            'SHERRIF_LETTER' => 'SHERRIF LETTER',
            'DEED_OF_SALE_BANK' => 'DEED OF SALE (BANK)',
        ];

        return $labels[$this->document_type] ?? $this->document_type;
    }
}
