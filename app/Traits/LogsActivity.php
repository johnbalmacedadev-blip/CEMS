<?php

namespace App\Traits;

use App\Models\ActivityLog;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log an activity
     */
    protected function logActivity($action, $model, $description = null, $changes = null, $section = null)
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $modelType = get_class($model);
        $modelId = $model->id ?? null;

        // Generate description if not provided
        if (!$description) {
            $modelName = class_basename($modelType);
            $description = $this->generateDescription($action, $modelName, $model);
        }

        // Get current page/route name
        $page = $this->getCurrentPage();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'page' => $page,
            'section' => $section,
            'changes' => $changes,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);

        ActivityLogger::markLogged(request());
    }

    /**
     * Get the current page/route name
     */
    protected function getCurrentPage()
    {
        $route = \Illuminate\Support\Facades\Route::current();
        if ($route) {
            $routeName = $route->getName();
            if ($routeName) {
                // Convert route name to readable format
                return ucwords(str_replace(['.', '-', '_'], ' ', $routeName));
            }
            // Fallback to URI
            $uri = $route->uri();
            return ucwords(str_replace(['/', '-', '_'], ' ', trim($uri, '/')));
        }
        
        // Fallback to request path
        $path = Request::path();
        return ucwords(str_replace(['/', '-', '_'], ' ', trim($path, '/')));
    }

    /**
     * Generate a description for the activity
     */
    protected function generateDescription($action, $modelName, $model)
    {
        $identifier = $this->getModelIdentifier($model);
        
        switch ($action) {
            case 'create':
                return "Created {$modelName}: {$identifier}";
            case 'update':
                return "Updated {$modelName}: {$identifier}";
            case 'delete':
                return "Deleted {$modelName}: {$identifier}";
            default:
                return "{$action} {$modelName}: {$identifier}";
        }
    }

    /**
     * Get an identifier for the model (name, title, or id)
     */
    protected function getModelIdentifier($model)
    {
        if (isset($model->name)) {
            return $model->name;
        }
        if (isset($model->title)) {
            return $model->title;
        }
        if (isset($model->plate_number)) {
            return $model->plate_number;
        }
        if (isset($model->email)) {
            return $model->email;
        }
        if (isset($model->description)) {
            return $model->description;
        }
        if (isset($model->original_name)) {
            return $model->original_name;
        }
        if (isset($model->amount)) {
            return "₱" . number_format($model->amount, 2);
        }
        if (isset($model->starting_balance)) {
            return "₱" . number_format($model->starting_balance, 2);
        }
        if (isset($model->id)) {
            return "ID: {$model->id}";
        }
        return 'Unknown';
    }

    /**
     * Log create activity
     */
    protected function logCreate($model, $description = null, $section = null)
    {
        $this->logActivity('create', $model, $description, null, $section);
    }

    /**
     * Log update activity
     */
    protected function logUpdate($model, $changes = null, $description = null, $section = null)
    {
        $this->logActivity('update', $model, $description, $changes, $section);
    }

    /**
     * Log delete activity
     */
    protected function logDelete($model, $description = null, $section = null)
    {
        $this->logActivity('delete', $model, $description, null, $section);
    }
}








