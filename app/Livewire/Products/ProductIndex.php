<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithPagination;

    public string $query = '';

    public function updatingQuery(): void
    {
        $this->resetPage();
    }

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
        $search = trim($this->query);

        $products = Product::with(['category', 'supplier'])
            ->where('company_id', auth()->user()->company_id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhereHas('supplier', fn ($supplier) => $supplier->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(50);

        return view('livewire.products.product-index', compact('products'));
    }
}
