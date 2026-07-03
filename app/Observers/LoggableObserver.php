<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoggableObserver
{
    public function created(Model $model): void
    {
        $this->logSafe($model, 'created');
    }

    public function updated(Model $model): void
    {
        $this->logSafe($model, 'updated');
    }

    public function deleted(Model $model): void
    {
        $this->logSafe($model, 'deleted');
    }

    public function restored(Model $model): void
    {
        $this->logSafe($model, 'restored');
    }

    protected function logSafe(Model $model, string $event): void
    {
        try {
            $this->log($model, $event);
        } catch (\Throwable $e) {
            Log::warning("Activity log skipped for {$event} on " . class_basename($model) . ': ' . $e->getMessage());
        }
    }

    protected function log(Model $model, string $event): void
    {
        $name = class_basename($model);

        $label = method_exists($model, 'activityLogDescription')
            ? $model->activityLogDescription($event)
            : ($model->getKey() ?? class_basename($model));

        $description = match ($event) {
            'created' => "{$name} '{$label}' dibuat",
            'updated' => "{$name} '{$label}' diubah",
            'deleted' => "{$name} '{$label}' dihapus",
            'restored' => "{$name} '{$label}' dipulihkan",
            default => "{$name} '{$label}' {$event}",
        };

        $changes = [];
        foreach ($model->getDirty() as $key => $newVal) {
            $changes[$key] = [
                'old' => $model->getOriginal($key),
                'new' => $newVal,
            ];
        }

        // Simpan metadata IP & info browser di properties
        $properties = [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        // Format $changes sesuai yang diharapkan Spatie v5 (attributes & old)
        $spatieChanges = [];
        if (!empty($changes)) {
            $spatieChanges = [
                'attributes' => collect($changes)->mapWithKeys(fn($item, $key) => [$key => $item['new']])->toArray(),
                'old' => collect($changes)->mapWithKeys(fn($item, $key) => [$key => $item['old']])->toArray(),
            ];
        }

        activity()
            ->causedBy(Auth::user())
            ->performedOn($model)
            ->withProperties($properties)
            ->withChanges($spatieChanges)
            ->event($event)
            ->log($description);
    }
}
