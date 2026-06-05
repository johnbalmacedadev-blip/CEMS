<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFormTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'document_type',
        'form_fields',
        'is_active',
    ];

    protected $casts = [
        'form_fields' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific document type
     */
    public function scopeForDocumentType($query, $documentType)
    {
        return $query->where('document_type', $documentType);
    }
}
