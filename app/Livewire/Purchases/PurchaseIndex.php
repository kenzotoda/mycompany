<?php

namespace App\Livewire\Purchases;

use App\Repositories\Contracts\PurchaseRepositoryInterface;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $purchases = app(PurchaseRepositoryInterface::class)
            ->paginateByCompany((int) auth()->user()->company_id, 10);

        return view('livewire.purchases.purchase-index', compact('purchases'));
    }
}
