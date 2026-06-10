<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Support\BrazilianDocument;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierIndex extends Component
{
    use WithPagination;

    public ?int $editingId = null;

    public string $name = '';

    public string $document = '';

    public string $phone = '';

    public string $email = '';

    public function edit(int $supplierId): void
    {
        $supplier = $this->findSupplier($supplierId);

        $this->editingId = $supplier->id;
        $this->name = $supplier->name;
        $this->document = $supplier->document;
        $this->phone = $supplier->phone ?? '';
        $this->email = $supplier->email ?? '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->reset('name', 'document', 'phone', 'email');
        $this->resetValidation();
    }

    public function save(): void
    {
        $companyId = auth()->user()->company_id;

        if (! $companyId) {
            $this->addError('name', 'Seu usuário não está vinculado a uma empresa.');

            return;
        }

        $supplier = $this->editingId ? $this->findSupplier($this->editingId) : null;

        $this->document = BrazilianDocument::formatCnpj($this->document);
        $this->phone = $this->phone ? BrazilianDocument::formatPhone($this->phone) : '';

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'document' => [
                'required',
                'string',
                'max:18',
                Rule::unique('suppliers', 'document')
                    ->where(fn ($query) => $query->where('company_id', $companyId))
                    ->ignore($supplier?->id),
            ],
            'phone' => ['nullable', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:255'],
        ], [
            'name.required' => 'Informe o nome do fornecedor.',
            'document.required' => 'Informe o CNPJ do fornecedor.',
            'document.unique' => 'Este CNPJ já está cadastrado para outro fornecedor.',
            'email.email' => 'Informe um e-mail válido.',
        ]);

        if (! BrazilianDocument::isCompleteCnpj($data['document'])) {
            $this->addError('document', 'O CNPJ deve ter 14 dígitos no formato 00.000.000/0000-00.');

            return;
        }

        if ($data['phone'] && ! BrazilianDocument::isCompletePhone($data['phone'])) {
            $this->addError('phone', 'Informe um telefone válido com DDD.');

            return;
        }

        $payload = [
            'name' => $data['name'],
            'document' => $data['document'],
            'phone' => $data['phone'] ?: null,
            'email' => $data['email'] ?: null,
        ];

        if ($supplier) {
            $supplier->update($payload);
            session()->flash('status', 'Fornecedor atualizado com sucesso.');
        } else {
            Supplier::create([
                'company_id' => $companyId,
                ...$payload,
            ]);
            session()->flash('status', 'Fornecedor cadastrado com sucesso.');
        }

        $this->cancelEdit();
    }

    public function delete(int $supplierId): void
    {
        $supplier = $this->findSupplier($supplierId);

        if ($this->editingId === $supplier->id) {
            $this->cancelEdit();
        }

        $supplier->delete();

        session()->flash('status', 'Fornecedor excluído com sucesso.');
    }

    private function findSupplier(int $supplierId): Supplier
    {
        return Supplier::query()
            ->where('company_id', auth()->user()->company_id)
            ->findOrFail($supplierId);
    }

    public function render()
    {
        $suppliers = Supplier::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.suppliers.supplier-index', compact('suppliers'));
    }
}
