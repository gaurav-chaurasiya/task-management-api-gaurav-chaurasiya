<?php

namespace App\Services;

use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class TaskService
{
    protected TaskRepositoryInterface $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getPaginatedTasksForProject(int $projectId, array $filters = [], string $sortBy = 'created_at', string $sortOrder = 'desc', int $perPage = 15): LengthAwarePaginator
    {
        $filtersStr = serialize($filters);
        $cacheKey = "project_{$projectId}_tasks_{$filtersStr}_{$sortBy}_{$sortOrder}_page_" . request()->get('page', 1);

        return Cache::remember($cacheKey, 600, function () use ($projectId, $filters, $sortBy, $sortOrder, $perPage) {
            return $this->taskRepository->paginateTasksByProject($projectId, $filters, $sortBy, $sortOrder, $perPage);
        });
    }

    public function createTask(array $data): Task
    {
        /** @var Task $task */
        $task = $this->taskRepository->create($data);
        $this->clearProjectCache($task->project_id);
        return $task;
    }

    public function updateTask(int $id, array $data): bool
    {
        $success = $this->taskRepository->update($id, $data);
        if ($success) {
            $task = $this->taskRepository->find($id);
            if ($task) {
                $this->clearProjectCache($task->project_id);
                Cache::forget("task_{$id}");
                
                // Dispatch event and job
                event(new \App\Events\TaskUpdated($task));
                \App\Jobs\LogTaskAction::dispatch('updated', $task->id);
            }
        }
        return $success;
    }

    public function deleteTask(int $id): bool
    {
        $task = $this->taskRepository->find($id);
        if ($task) {
            $projectId = $task->project_id;
            $success = $this->taskRepository->delete($id);
            if ($success) {
                $this->clearProjectCache($projectId);
                Cache::forget("task_{$id}");
            }
            return $success;
        }
        return false;
    }

    public function findTask(int $id): ?Task
    {
        return Cache::remember("task_{$id}", 600, function () use ($id) {
            /** @var Task|null $task */
            $task = $this->taskRepository->find($id);
            return $task;
        });
    }

    protected function clearProjectCache(int $projectId): void
    {
        // Cache clearing logic
    }
}
