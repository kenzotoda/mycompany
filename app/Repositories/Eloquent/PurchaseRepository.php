<?php

namespace App\Repositories\Eloquent;

use App\Models\Purchase;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PurchaseRepository implements PurchaseRepositoryInterface
{
    public function paginateByCompany(int $companyId, int $perPage = 10): LengthAwarePaginator
    {
        return Purchase::with(['supplier', 'items.product', 'attachments'])
            ->where('company_id', $companyId)
            ->latest('purchase_date')
            ->paginate($perPage);
    }

    public function create(array $data): Purchase
    {
        return Purchase::create($data);
    }
}
