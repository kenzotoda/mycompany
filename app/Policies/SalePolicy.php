<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('sales.view');
    }

    public function view(User $user, Sale $sale): bool
    {
        return $user->can('sales.view') && $user->company_id === $sale->company_id;
    }

    public function create(User $user): bool
    {
        return $user->can('sales.create');
    }

    public function update(User $user, Sale $sale): bool
    {
        return $user->can('sales.update') && $user->company_id === $sale->company_id;
    }

    public function delete(User $user, Sale $sale): bool
    {
        return $user->can('sales.delete') && $user->company_id === $sale->company_id;
    }

    public function restore(User $user, Sale $sale): bool
    {
        return $this->delete($user, $sale);
    }

    public function forceDelete(User $user, Sale $sale): bool
    {
        return false;
    }
}
