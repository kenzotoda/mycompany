<?php

namespace App\Livewire\Shared;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
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
            $products = Product::where('company_id', $companyId)->where('name', 'like', "%{$query}%")->limit(5)->get();
            $customers = Customer::where('company_id', $companyId)->where('name', 'like', "%{$query}%")->limit(5)->get();
            $suppliers = Supplier::where('company_id', $companyId)->where('name', 'like', "%{$query}%")->limit(5)->get();
        }

        return view('livewire.shared.global-search', compact('products', 'customers', 'suppliers'));
    }
}
