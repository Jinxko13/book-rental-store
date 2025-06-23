<?php

namespace App\Repositories\Eloquent;

use App\Models\ActivityLog;
use App\Repositories\Interfaces\ActivityLogRepositoryInterface;

class ActivityLogRepository implements ActivityLogRepositoryInterface
{
    protected $model;

    public function __construct(ActivityLog $model)
    {
        $this->model = $model;
    }

    public function all(array $filters = [])
    {
        return $this->model->query()
            ->when($filters['from_date'] ?? null, fn($q) => $q->whereDate('date_log', '>=', $filters['from_date']))
            ->when($filters['to_date'] ?? null, fn($q) => $q->whereDate('date_log', '<=', $filters['to_date']))
            ->when($filters['user_id'] ?? null, fn($q) => $q->where('user_id', $filters['user_id']))
            ->when($filters['action'] ?? null, fn($q) => $q->where('action', 'like', '%' . $filters['action'] . '%'))
            ->when($filters['computer_name'] ?? null, fn($q) => $q->where('computer_name', 'like', '%' . $filters['computer_name'] . '%'))
            ->when($filters['ip_address'] ?? null, fn($q) => $q->where('ip_address', $filters['ip_address']))
            ->latest('date_log')
            ->paginate(20)
        ;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    // public function update($id, array $data)
    // {
    //     $record = $this->model->findOrFail($id);
    //     $record->update($data);
    //     return $record;
    // }

    // public function delete($id)
    // {
    //     return $this->model->destroy($id);
    // }
}