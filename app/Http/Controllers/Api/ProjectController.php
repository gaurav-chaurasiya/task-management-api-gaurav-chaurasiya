<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreProjectRequest;
use App\Http\Requests\Api\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index(): AnonymousResourceCollection
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $projects = $this->projectService->getPaginatedProjectsForUser($user->id);
        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request): ProjectResource
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $data = $request->validated();
        $data['user_id'] = $user->id;
        
        $project = $this->projectService->createProject($data);
        return new ProjectResource($project);
    }

    public function show(Project $project): ProjectResource
    {
        $this->authorize('view', $project);
        $project = $this->projectService->findProject($project->id);
        return new ProjectResource($project->loadCount('tasks'));
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $this->authorize('update', $project);
        $this->projectService->updateProject($project->id, $request->validated());
        $project = $this->projectService->findProject($project->id);
        return new ProjectResource($project);
    }

    public function destroy(Project $project): Response
    {
        $this->authorize('delete', $project);
        $this->projectService->deleteProject($project->id);
        return response()->noContent();
    }
}
