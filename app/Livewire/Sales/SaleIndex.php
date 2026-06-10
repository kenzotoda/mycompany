<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;

class SaleIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $sales = Sale::with(['customer', 'items.product', 'attachments'])
            ->where('company_id', auth()->user()->company_id)
            ->latest('sale_date')
            ->paginate(10);

        return view('livewire.sales.sale-index', compact('sales'));
    }
}
