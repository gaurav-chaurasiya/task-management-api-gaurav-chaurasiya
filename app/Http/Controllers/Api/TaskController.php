<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTaskRequest;
use App\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    use AuthorizesRequests;

    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request, Project $project): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Task::class, $project]);

        $filters = $request->only(['status', 'priority']);
        $sortBy = (string) $request->get('sort_by', 'created_at');
        $sortOrder = (string) $request->get('sort_order', 'desc');

        $tasks = $this->taskService->getPaginatedTasksForProject($project->id, $filters, $sortBy, $sortOrder);

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request, Project $project): TaskResource
    {
        $this->authorize('create', [Task::class, $project]);

        $data = $request->validated();
        $data['project_id'] = $project->id;

        $task = $this->taskService->createTask($data);

        return new TaskResource($task);
    }

    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task);

        $task = $this->taskService->findTask($task->id);

        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);

        $this->taskService->updateTask($task->id, $request->validated());
        
        $task = $this->taskService->findTask($task->id);

        return new TaskResource($task);
    }

    public function destroy(Task $task): Response
    {
        $this->authorize('delete', $task);

        $this->taskService->deleteTask($task->id);

        return response()->noContent();
    }
}
