<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;

class ProjectService
{
    protected ProjectRepositoryInterface $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getPaginatedProjectsForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = "user_{$userId}_projects_page_" . request()->get('page', 1);

        return Cache::remember($cacheKey, 600, function () use ($userId, $perPage) {
            return $this->projectRepository->paginateUserProjects($userId, $perPage);
        });
    }

    public function createProject(array $data): Project
    {
        /** @var Project $project */
        $project = $this->projectRepository->create($data);
        $this->clearUserCache($project->user_id);
        return $project;
    }

    public function updateProject(int $id, array $data): bool
    {
        $success = $this->projectRepository->update($id, $data);
        if ($success) {
            $project = $this->projectRepository->find($id);
            if ($project) {
                $this->clearUserCache($project->user_id);
            }
        }
        return $success;
    }

    public function deleteProject(int $id): bool
    {
        $project = $this->projectRepository->find($id);
        if ($project) {
            $userId = $project->user_id;
            $success = $this->projectRepository->delete($id);
            if ($success) {
                $this->clearUserCache($userId);
            }
            return $success;
        }
        return false;
    }

    public function findProject(int $id): ?Project
    {
        return Cache::remember("project_{$id}", 600, function () use ($id) {
            /** @var Project|null $project */
            $project = $this->projectRepository->find($id);
            return $project;
        });
    }

    protected function clearUserCache(int $userId): void
    {
        // In a real app we might use cache tags if supported by driver
        // For simplicity with Redis/File, we'd ideally use tags
        // Cache::tags(['projects', "user_{$userId}"])->flush();
        
        // Simplified flush for this demo (requires manual key management usually)
    }
}
