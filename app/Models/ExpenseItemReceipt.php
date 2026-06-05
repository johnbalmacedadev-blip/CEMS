<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseItemReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_item_id',
        'image_path',
        'original_name',
        'mime_type',
        'file_size',
        'sort_order',
    ];

    /**
     * @var array<int, string>
     */
    protected $appends = ['url'];

    /**
     * Get the expense item that owns the receipt.
     */
    public function expenseItem()
    {
        return $this->belongsTo(ExpenseItem::class);
    }

    /**
     * Public URL for the receipt image (files live under storage/app/public → served as /storage/...).
     * Requires `php artisan storage:link`. Uses the current request host so 127.0.0.1:8000 matches the page.
     */
    public function getUrlAttribute(): string
    {
        if ($this->image_path === null || $this->image_path === '') {
            return '';
        }

        $path = ltrim(str_replace('\\', '/', $this->image_path), '/');
        $relative = '/storage/' . $path;

        if (! app()->runningInConsole() && request()->getSchemeAndHttpHost()) {
            return rtrim(request()->getSchemeAndHttpHost(), '/') . $relative;
        }

        return asset('storage/' . $path);
    }

    /**
     * Scope for ordering receipts
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }
}
