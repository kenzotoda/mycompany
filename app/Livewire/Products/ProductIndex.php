<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithPagination;

    public function delete(int $productId): void
    {
        $product = Product::query()
            ->where('company_id', auth()->user()->company_id)
            ->findOrFail($productId);

        $this->authorize('delete', $product);

        $product->forceDelete();

        session()->flash('status', 'Produto excluído com sucesso.');
    }

    public function render()
    {
        $products = Product::with('category')
            ->where('company_id', auth()->user()->company_id)
            ->latest()
            ->paginate(10);

        return view('livewire.products.product-index', compact('products'));
    }
}
