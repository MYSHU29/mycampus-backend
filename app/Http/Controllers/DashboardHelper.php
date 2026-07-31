<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Auth;

class DashboardHelper
{
    /**
     * Get activity log statistics for dashboard
     */
    public static function getActivityStats($days = 7)
    {
        return [
            'total_activities' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week' => ActivityLog::whereDate('created_at', '>=', now()->subDays($days))->count(),
            'recent' => ActivityLog::latest('created_at')->limit(10)->get(),
        ];
    }

    /**
     * Log user action
     */
    public static function logAction($activity, $module, $description, $dataBefore = null, $dataAfter = null)
    {
        if (Auth::check()) {
            ActivityLog::log($activity, $module, $description, $dataBefore, $dataAfter);
        }
    }

    /**
     * Get user statistics
     */
    public static function getUserStats()
    {
        return [
            'total_users' => \App\Models\User::count(),
            'active_today' => ActivityLog::where('activity_type', 'login')
                ->whereDate('created_at', today())
                ->distinct('user_id')
                ->count(),
        ];
    }
}
