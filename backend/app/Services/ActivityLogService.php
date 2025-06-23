<?php

namespace App\Services;

use App\Repositories\Interfaces\ActivityLogRepositoryInterface;

class ActivityLogService
{
    protected $ActivityLogRepo;

    public function __construct(ActivityLogRepositoryInterface $ActivityLogRepo)
    {
        $this->ActivityLogRepo = $ActivityLogRepo;
    }

    public function getAll(array $filters = [])
    {
        return $this->ActivityLogRepo->all($filters);
    }

    public function findById($id)
    {
        return $this->ActivityLogRepo->find($id);
    }

    public function create(array $data)
    {
        return $this->ActivityLogRepo->create($data);
    }

    public function logChange(string $action, $model, array $payload): void
    {
        $this->ActivityLogRepo->create([
            'action'        => $action,
            'model_type'    => class_basename($model),
            'model_id'      => $model->id,
            'payload'       => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'user_id'       => auth()->id(),
            'ip_address'    => request()->ip(),
            'computer_name' => gethostname(),
            'date_log'      => now(),
            'active'        => true,
        ]);
    }
}