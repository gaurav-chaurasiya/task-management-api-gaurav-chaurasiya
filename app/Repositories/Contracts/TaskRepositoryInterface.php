<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface extends BaseRepositoryInterface
{
    public function getTasksByProject(int $projectId, array $filters = [], string $sortBy = 'created_at', string $sortOrder = 'desc'): Collection;
    public function paginateTasksByProject(int $projectId, array $filters = [], string $sortBy = 'created_at', string $sortOrder = 'desc', int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}
