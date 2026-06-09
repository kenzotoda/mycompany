<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $products = Product::with('category')
            ->where('company_id', auth()->user()->company_id)
            ->latest()
            ->paginate(10);

        return view('livewire.products.product-index', compact('products'));
    }
}
