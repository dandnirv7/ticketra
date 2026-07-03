<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LoggableObserver
{
    public function created(Model $model): void
    {
        $this->log($model, 'created');
    }

    public function updated(Model $model): void
    {
        $this->log($model, 'updated');
    }

    public function deleted(Model $model): void
    {
        $this->log($model, 'deleted');
    }

    public function restored(Model $model): void
    {
        $this->log($model, 'restored');
    }

    protected function log(Model $model, string $event): void
    {
        $name = class_basename($model);

        if (!config("activitylog.auto_log.{$name}", true)) {
            return;
        }

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

        $performer = Auth::user();

        $changes = [];
        foreach ($model->getDirty() as $key => $newVal) {
            $changes[$key] = [
                'old' => $model->getOriginal($key),
                'new' => $newVal,
            ];
        }

        activity()
            ->causedBy($performer)
            ->performedOn($model)
            ->withProperties([
                'ip' => request()->ip(),
            ])
            ->withChanges($changes)
            ->event($event)
            ->log($description);
    }
}
