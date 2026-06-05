<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'contract_start',
        'contract_type',
        'role',
        'location',
        'sss',
        'philhealth',
        'pagibig',
        'birthdate',
        'status',
        'notes',
        'primary_photo'
    ];

    protected $casts = [
        'contract_start' => 'date',
        'birthdate' => 'date',
    ];

    // Accessor for full name
    public function getFullNameAttribute()
    {
        $name = $this->first_name;
        if ($this->middle_name && $this->middle_name !== 'N/A') {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }

    // Accessor for formatted contract start date
    public function getFormattedContractStartAttribute()
    {
        return $this->contract_start ? $this->contract_start->format('F j, Y') : 'N/A';
    }

    // Accessor for formatted birthdate
    public function getFormattedBirthdateAttribute()
    {
        return $this->birthdate ? $this->birthdate->format('F j, Y') : 'N/A';
    }

    // Accessor for age
    public function getAgeAttribute()
    {
        return $this->birthdate ? $this->birthdate->age : null;
    }

    // Accessor for contract duration
    public function getContractDurationAttribute()
    {
        if (!$this->contract_start) {
            return 'N/A';
        }
        
        $now = now();
        $duration = $this->contract_start->diffInDays($now);
        
        if ($duration < 30) {
            return $duration . ' days';
        } elseif ($duration < 365) {
            $months = $this->contract_start->diffInMonths($now);
            return $months . ' month' . ($months > 1 ? 's' : '');
        } else {
            $years = $this->contract_start->diffInYears($now);
            $months = $this->contract_start->diffInMonths($now) % 12;
            $result = $years . ' year' . ($years > 1 ? 's' : '');
            if ($months > 0) {
                $result .= ' ' . $months . ' month' . ($months > 1 ? 's' : '');
            }
            return $result;
        }
    }

    // Scope for active employees
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for inactive employees
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Scope for probationary employees
    public function scopeProbationary($query)
    {
        return $query->where('contract_type', 'PROBATIONARY');
    }

    // Scope for regular employees
    public function scopeRegular($query)
    {
        return $query->where('contract_type', 'REGULAR');
    }

    /**
     * Get the full URL for the primary photo.
     */
    public function getPrimaryPhotoUrlAttribute()
    {
        if ($this->primary_photo) {
            return asset('storage/' . $this->primary_photo);
        }
        return null;
    }
}