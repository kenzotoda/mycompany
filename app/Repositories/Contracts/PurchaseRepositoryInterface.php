<?php

namespace App\Repositories\Contracts;

use App\Models\Purchase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PurchaseRepositoryInterface
{
    public function paginateByCompany(int $companyId, int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Purchase;
}
