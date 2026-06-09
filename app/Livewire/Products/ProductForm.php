<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Component;

class ProductForm extends Component
{
    public string $name = '';
    public string $sku = '';

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50'],
        ]);

        Product::create([
            'company_id' => auth()->user()->company_id,
            'name' => $data['name'],
            'sku' => $data['sku'] ?: 'SKU-'.strtoupper(Str::random(8)),
        ]);

        $this->reset('name', 'sku');

        session()->flash('status', 'Produto cadastrado com sucesso.');
        $this->redirectRoute('products.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.products.product-form');
    }
}
