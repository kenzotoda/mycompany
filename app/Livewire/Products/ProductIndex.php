<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Support\SearchQuery;
use Illuminate\Support\Facades\Schema;
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
        $companyId = auth()->user()->company_id;
        $hasSupplierColumn = Schema::hasColumn('products', 'supplier_id');
        $relations = ['category'];

        if ($hasSupplierColumn) {
            $relations[] = 'supplier';
        }

        $productsQuery = Product::query()
            ->with($relations)
            ->where('company_id', $companyId);

        if ($search !== '') {
            $productsQuery->where(function ($inner) use ($search, $hasSupplierColumn) {
                SearchQuery::whereLikeInsensitive($inner, 'name', $search);
                SearchQuery::orWhereLikeInsensitive($inner, 'sku', $search);

                if ($hasSupplierColumn) {
                    $inner->orWhereHas(
                        'supplier',
                        fn ($supplier) => SearchQuery::whereLikeInsensitive($supplier, 'name', $search)
                    );
                }
            });
        }

        $products = $productsQuery
            ->latest()
            ->paginate(50);

        return view('livewire.products.product-index', [
            'products' => $products,
            'hasSupplierColumn' => $hasSupplierColumn,
        ]);
    }
}
