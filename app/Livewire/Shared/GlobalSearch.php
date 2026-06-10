<?php

namespace App\Livewire\Shared;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Support\SearchQuery;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    public function render()
    {
        $companyId = auth()->user()->company_id;
        $query = trim($this->query);

        $products = [];
        $customers = [];
        $suppliers = [];

        if (mb_strlen($query) >= 2) {
            $products = Product::query()
                ->where('company_id', $companyId)
                ->tap(fn ($builder) => SearchQuery::whereLikeInsensitive($builder, 'name', $query))
                ->limit(5)
                ->get();
            $customers = Customer::query()
                ->where('company_id', $companyId)
                ->tap(fn ($builder) => SearchQuery::whereLikeInsensitive($builder, 'name', $query))
                ->limit(5)
                ->get();
            $suppliers = Supplier::query()
                ->where('company_id', $companyId)
                ->tap(fn ($builder) => SearchQuery::whereLikeInsensitive($builder, 'name', $query))
                ->limit(5)
                ->get();
        }

        return view('livewire.shared.global-search', compact('products', 'customers', 'suppliers'));
    }
}
