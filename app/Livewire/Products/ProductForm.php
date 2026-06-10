<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProductForm extends Component
{
    public ?int $productId = null;

    public string $name = '';

    public string $sku = '';

    public function mount(?int $productId = null): void
    {
        $this->productId = $productId;

        if (! $productId) {
            return;
        }

        $product = $this->findProduct();

        $this->authorize('update', $product);

        $this->name = $product->name;
        $this->sku = $product->sku;
    }

    public function save(): void
    {
        $companyId = auth()->user()->company_id;

        if (! $companyId) {
            $this->addError('name', 'Seu usuário não está vinculado a uma empresa.');

            return;
        }

        $product = $this->productId ? $this->findProduct() : null;

        if ($product) {
            $this->authorize('update', $product);
        } else {
            $this->authorize('create', Product::class);
        }

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'sku')
                    ->where(fn ($query) => $query->where('company_id', $companyId))
                    ->ignore($product?->id),
            ],
        ], [
            'name.required' => 'Informe o nome do produto.',
            'sku.unique' => 'Este SKU já está em uso por outro produto.',
        ]);

        $sku = $data['sku'] ?: ($product?->sku ?? 'SKU-'.strtoupper(Str::random(8)));

        if ($product) {
            $product->update([
                'name' => $data['name'],
                'sku' => $sku,
            ]);

            session()->flash('status', 'Produto atualizado com sucesso.');
        } else {
            Product::create([
                'company_id' => $companyId,
                'name' => $data['name'],
                'sku' => $sku,
            ]);

            session()->flash('status', 'Produto cadastrado com sucesso.');
        }

        $this->redirectRoute('products.index');
    }

    private function findProduct(): Product
    {
        return Product::query()
            ->where('company_id', auth()->user()->company_id)
            ->findOrFail($this->productId);
    }

    public function render()
    {
        return view('livewire.products.product-form', [
            'editing' => $this->productId !== null,
        ]);
    }
}
