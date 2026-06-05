<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityObserver
{
    /**
     * Handle the model "created" event.
     */
    public function created($model)
    {
        $this->logActivity('create', $model);
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated($model)
    {
        // Only log if there are actual changes (not just timestamps)
        $changes = $model->getChanges();
        unset($changes['updated_at']); // Remove timestamp changes
        
        if (!empty($changes)) {
            $original = $model->getOriginal();
            $formattedChanges = [];
            
            foreach ($changes as $key => $newValue) {
                $oldValue = $original[$key] ?? null;
                if ($oldValue != $newValue) {
                    $formattedChanges[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            }
            
            if (!empty($formattedChanges)) {
                $this->logActivity('update', $model, null, $formattedChanges);
            }
        }
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted($model)
    {
        $this->logActivity('delete', $model);
    }

    /**
     * Log an activity
     */
    protected function logActivity($action, $model, $description = null, $changes = null)
    {
        // Skip if user is not authenticated (e.g., during seeding)
        if (!Auth::check()) {
            return;
        }

        // Skip if model is ActivityLog itself (prevent infinite recursion)
        if ($model instanceof ActivityLog) {
            return;
        }

        // Skip if model has a flag indicating manual logging was done
        if (isset($model->skipActivityLog) && $model->skipActivityLog === true) {
            return;
        }

        $user = Auth::user();
        $modelType = get_class($model);
        $modelId = $model->id ?? null;

        // Check if a log was recently created for this same action, model, and user (within last 2 seconds)
        // This prevents duplicate logs from both manual logging and observer
        $recentLog = ActivityLog::where('user_id', $user->id)
            ->where('action', $action)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->where('created_at', '>=', now()->subSeconds(2))
            ->first();

        // If a recent log exists, skip observer logging (manual logging was likely done)
        if ($recentLog) {
            return;
        }

        // Generate description if not provided
        if (!$description) {
            $modelName = class_basename($modelType);
            $identifier = $this->getModelIdentifier($model);
            $description = $this->generateDescription($action, $modelName, $identifier);
        }

        try {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'description' => $description,
                'changes' => $changes,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail to prevent breaking the application
            // Log to Laravel's log if needed
            \Log::warning('Failed to log activity: ' . $e->getMessage());
        }
    }

    /**
     * Generate a description for the activity
     */
    protected function generateDescription($action, $modelName, $identifier)
    {
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
     * Get an identifier for the model
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
}

