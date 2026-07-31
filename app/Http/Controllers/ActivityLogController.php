<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter berdasarkan module
        if ($request->filled('module')) {
            $query->byModule($request->module);
        }

        // Filter berdasarkan activity type
        if ($request->filled('activity_type')) {
            $query->byActivityType($request->activity_type);
        }

        // Filter berdasarkan user
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start_date = Carbon::parse($request->start_date)->startOfDay();
            $end_date = Carbon::parse($request->end_date)->endOfDay();
            $query->inDateRange($start_date, $end_date);
        }

        $activityLogs = $query->latest('created_at')->paginate(50);
        $users = User::orderBy('name')->get();

        // Mendapatkan unique modules dan activity types untuk filter
        $modules = ActivityLog::distinct()->pluck('module')->sort();
        $activityTypes = ActivityLog::distinct()->pluck('activity_type')->sort();

        return view('operator.activity-logs.index', compact('activityLogs', 'users', 'modules', 'activityTypes'));
    }

    /**
     * Show detail activity log
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        return view('operator.activity-logs.show', compact('activityLog'));
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
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
            $start_date = Carbon::parse($request->start_date)->startOfDay();
            $end_date = Carbon::parse($request->end_date)->endOfDay();
            $query->inDateRange($start_date, $end_date);
        }

        $activityLogs = $query->latest('created_at')->get();

        $csv = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="activity_logs.csv"');

        // Header
        fputcsv($csv, ['Tanggal', 'User', 'Module', 'Activity Type', 'Description', 'IP Address']);

        // Data
        foreach ($activityLogs as $log) {
            fputcsv($csv, [
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user->name ?? 'Unknown',
                $log->module,
                $log->activity_type,
                $log->description,
                $log->ip_address,
            ]);
        }

        fclose($csv);
    }

    /**
     * Delete old activity logs (older than specified days)
     */
    public function deleteOldLogs(Request $request)
    {
        $days = $request->input('days', 90);
        $cutoffDate = Carbon::now()->subDays($days);

        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        return redirect()->back()->with('success', "Berhasil menghapus {$deletedCount} log aktivitas lama.");
    }
}
