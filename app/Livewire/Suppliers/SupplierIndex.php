<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Support\BrazilianDocument;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierIndex extends Component
{
    use WithPagination;

    public string $name = '';
    public string $document = '';
    public string $phone = '';
    public string $email = '';

    public function save(): void
    {
        $this->document = BrazilianDocument::formatCnpj($this->document);
        $this->phone = $this->phone ? BrazilianDocument::formatPhone($this->phone) : '';

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'size:18'],
            'phone' => ['nullable', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:255'],
        ], [
            'document.required' => 'Informe o CNPJ do fornecedor.',
            'document.size' => 'O CNPJ deve ter 14 dígitos no formato 00.000.000/0000-00.',
        ]);

        if (! BrazilianDocument::isCompleteCnpj($data['document'])) {
            $this->addError('document', 'O CNPJ deve ter 14 dígitos no formato 00.000.000/0000-00.');

            return;
        }

        if ($data['phone'] && ! BrazilianDocument::isCompletePhone($data['phone'])) {
            $this->addError('phone', 'Informe um telefone válido com DDD.');

            return;
        }

        Supplier::create([
            'company_id' => auth()->user()->company_id,
            'name' => $data['name'],
            'document' => $data['document'],
            'phone' => $data['phone'] ?: null,
            'email' => $data['email'] ?: null,
        ]);

        $this->reset('name', 'document', 'phone', 'email');
        session()->flash('status', 'Fornecedor cadastrado com sucesso.');
    }

    public function render()
    {
        $suppliers = Supplier::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.suppliers.supplier-index', compact('suppliers'));
    }
}
