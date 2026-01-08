<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface extends BaseRepositoryInterface
{
    public function getUserProjects(int $userId): Collection;
    public function paginateUserProjects(int $userId, int $perPage = 15): LengthAwarePaginator;
}
