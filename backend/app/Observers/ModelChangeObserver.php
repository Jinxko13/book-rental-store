<?php

namespace App\Observers;

use App\Jobs\UpdateActivityLog;
use Illuminate\Database\Eloquent\Model;

class ModelChangeObserver
{
    public function created(Model $model)
    {
        $this->log('CREATED', $model, $model->getAttributes());
    }

    public function updated(Model $model)
    {
        $this->log('UPDATED', $model, $model->getChanges());
    }

    public function deleted(Model $model)
    {
        $this->log('DELETED', $model, $model->getOriginal());
    }

    protected function log(string $action, Model $model, array $payload)
    {
        UpdateActivityLog::dispatch(
            action: $action,
            modelType: class_basename($model),
            modelId: $model->id ?? 0,
            payload: $payload,
            userId: auth()->id() ?? null,
            ipAddress: request()->ip() ?? null,
            computerName: gethostname()
        );
    }
}
