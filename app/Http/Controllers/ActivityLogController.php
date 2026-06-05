<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by action if provided
        if ($request->has('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        // Filter by model type if provided
        if ($request->has('model_type') && $request->model_type !== 'all') {
            $query->where('model_type', $request->model_type);
        }

        // Filter by current user if requested
        if ($request->has('user_filter') && $request->user_filter === 'current') {
            $query->where('user_id', auth()->id());
        }

        $logs = $query->paginate(20);

        // Get unique model types for filter
        $modelTypes = ActivityLog::select('model_type')
            ->distinct()
            ->orderBy('model_type')
            ->pluck('model_type')
            ->map(function ($type) {
                return $type; // Return full class name for filtering
            })
            ->unique()
            ->values();

        return view('placeholders.admin-docs', compact('logs', 'modelTypes'));
    }
}
