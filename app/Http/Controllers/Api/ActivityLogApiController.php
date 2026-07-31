<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::with('user');

        if ($request->filled('module')) {
            $query->byModule($request->module);
        }

        if ($request->filled('activity_type')) {
            $query->byActivityType($request->activity_type);
        }

        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->inDateRange($request->start_date, $request->end_date);
        }

        $logs = $query->latest('created_at')->paginate($request->get('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    public function filters(): JsonResponse
    {
        $modules = ActivityLog::distinct()->pluck('module')->sort()->values();
        $activityTypes = ActivityLog::distinct()->pluck('activity_type')->sort()->values();
        $users = User::orderBy('name')->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => [
                'modules' => $modules,
                'activity_types' => $activityTypes,
                'users' => $users,
            ],
        ]);
    }
}
