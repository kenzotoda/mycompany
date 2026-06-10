<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProductForm extends Component
{
    public ?int $productId = null;

    public string $name = '';

    public string $sku = '';
    
    public string $stock_quantity = '0';

    public ?int $supplier_id = null;

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
        $this->stock_quantity = (string) $product->stockUnits();
        $this->supplier_id = $product->supplier_id;
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

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'supplier_id' => [
                'nullable',
                Rule::exists('suppliers', 'id')->where(fn ($query) => $query->where('company_id', $companyId)),
            ],
            'sku' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'sku')
                    ->where(fn ($query) => $query->where('company_id', $companyId))
                    ->ignore($product?->id),
            ],
        ];

        if ($product) {
            $rules['stock_quantity'] = ['required', 'integer', 'min:0'];
        }

        $data = $this->validate($rules, [
            'name.required' => 'Informe o nome do produto.',
            'supplier_id.exists' => 'Selecione um fornecedor válido.',
            'sku.unique' => 'Este SKU já está em uso por outro produto.',
            'stock_quantity.required' => 'Informe o estoque atual do produto.',
            'stock_quantity.integer' => 'O estoque deve ser um número inteiro.',
            'stock_quantity.min' => 'O estoque não pode ser negativo.',
        ]);

        $sku = $data['sku'] ?: ($product?->sku ?? 'SKU-'.strtoupper(Str::random(8)));

        if ($product) {
            $product->update([
                'name' => $data['name'],
                'supplier_id' => $data['supplier_id'] ?: null,
                'sku' => $sku,
                'stock_quantity' => (int) $data['stock_quantity'],
            ]);

            session()->flash('status', 'Produto atualizado com sucesso.');
        } else {
            Product::create([
                'company_id' => $companyId,
                'name' => $data['name'],
                'supplier_id' => $data['supplier_id'] ?: null,
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
            'suppliers' => Supplier::query()
                ->where('company_id', auth()->user()->company_id)
                ->orderBy('name')
                ->get(),
        ]);
    }
}
