<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'activity_type',
        'module',
        'description',
        'data_before',
        'data_after',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data_before' => 'array',
        'data_after' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Fungsi helper untuk mencatat aktivitas
     */
    public static function log($activity_type, $module, $description, $data_before = null, $data_after = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'activity_type' => $activity_type,
            'module' => $module,
            'description' => $description,
            'data_before' => $data_before,
            'data_after' => $data_after,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Scope untuk filter berdasarkan module
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope untuk filter berdasarkan activity type
     */
    public function scopeByActivityType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    /**
     * Scope untuk mendapatkan aktivitas dalam rentang waktu
     */
    public function scopeInDateRange($query, $start_date, $end_date)
    {
        return $query->whereBetween('created_at', [$start_date, $end_date]);
    }
}
