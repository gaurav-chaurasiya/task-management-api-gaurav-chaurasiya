<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentTaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    public function getTasksByProject(int $projectId, array $filters = [], string $sortBy = 'created_at', string $sortOrder = 'desc'): Collection
    {
        return $this->getTasksQuery($projectId, $filters, $sortBy, $sortOrder)->get();
    }

    public function paginateTasksByProject(int $projectId, array $filters = [], string $sortBy = 'created_at', string $sortOrder = 'desc', int $perPage = 15): LengthAwarePaginator
    {
        return $this->getTasksQuery($projectId, $filters, $sortBy, $sortOrder)->paginate($perPage);
    }

    protected function getTasksQuery(int $projectId, array $filters = [], string $sortBy = 'created_at', string $sortOrder = 'desc')
    {
        $query = $this->model->where('project_id', $projectId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (in_array($sortBy, ['due_date', 'priority', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }
}
