<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Get the activity logs for the user
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get page-level permissions for this user (non-admin only; admin has full access)
     */
    public function pagePermissions()
    {
        return $this->hasMany(UserPagePermission::class);
    }

    /**
     * Check if user can perform action on a page.
     * Admins always have full access. Others use user_page_permissions.
     *
     * @param string $pageSlug  Page slug from config('pages.list')
     * @param string $action    One of: view, create, update, delete
     * @return bool
     */
    public function canAccessPage(string $pageSlug, string $action = 'view'): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $perm = $this->pagePermissions()->where('page_slug', $pageSlug)->first();
        if (! $perm) {
            // Allow view for home/dashboard so new users can at least land after login
            if (in_array($pageSlug, ['home', 'dashboard'], true) && $action === 'view') {
                return true;
            }
            return false;
        }

        return match ($action) {
            'view' => $perm->can_view,
            'create' => $perm->can_create,
            'update' => $perm->can_update,
            'delete' => $perm->can_delete,
            default => false,
        };
    }

    /**
     * Get permission for a page (for editing in UI). Returns array with can_view, can_create, can_update, can_delete.
     */
    public function getPagePermission(string $pageSlug): array
    {
        return [
            'can_view' => $this->canAccessPage($pageSlug, 'view'),
            'can_create' => $this->canAccessPage($pageSlug, 'create'),
            'can_update' => $this->canAccessPage($pageSlug, 'update'),
            'can_delete' => $this->canAccessPage($pageSlug, 'delete'),
        ];
    }
}

