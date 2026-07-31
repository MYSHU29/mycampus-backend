<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Automatically log model creation
     */
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            if (auth()->check()) {
                ActivityLog::log(
                    'create',
                    class_basename($model),
                    "Membuat " . class_basename($model) . " baru",
                    null,
                    $model->toArray()
                );
            }
        });

        static::updated(function ($model) {
            if (auth()->check()) {
                $original = $model->getOriginal();
                ActivityLog::log(
                    'update',
                    class_basename($model),
                    "Mengubah " . class_basename($model),
                    $original,
                    $model->toArray()
                );
            }
        });

        static::deleted(function ($model) {
            if (auth()->check()) {
                ActivityLog::log(
                    'delete',
                    class_basename($model),
                    "Menghapus " . class_basename($model),
                    $model->toArray(),
                    null
                );
            }
        });
    }
}
