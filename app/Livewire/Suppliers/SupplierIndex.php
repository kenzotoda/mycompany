<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Support\BrazilianDocument;
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
        $this->document = BrazilianDocument::digitsOnly($supplier->document);
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

        $this->normalizeFields();

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:14'],
            'phone' => ['nullable', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:255'],
        ], [
            'name.required' => 'Informe o nome do fornecedor.',
            'email.email' => 'Informe um e-mail válido.',
        ]);

        $documentDigits = BrazilianDocument::digitsOnly($data['document']);

        if ($documentDigits === '') {
            $this->addError('document', 'Informe o CNPJ do fornecedor.');

            return;
        }

        if (! BrazilianDocument::isCompleteCnpj($documentDigits)) {
            $this->addError('document', 'O CNPJ deve ter 14 dígitos no formato 00.000.000/0000-00.');

            return;
        }

        if ($this->documentAlreadyExists($documentDigits, $companyId, $supplier?->id)) {
            $this->addError('document', 'Este CNPJ já está cadastrado para outro fornecedor.');

            return;
        }

        if ($data['phone'] && ! BrazilianDocument::isCompletePhone($data['phone'])) {
            $this->addError('phone', 'Informe um telefone válido com DDD.');

            return;
        }

        $payload = [
            'name' => $data['name'],
            'document' => $documentDigits,
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

        $supplier->forceDelete();

        session()->flash('status', 'Fornecedor excluído com sucesso.');
    }

    private function findSupplier(int $supplierId): Supplier
    {
        return Supplier::query()
            ->where('company_id', auth()->user()->company_id)
            ->findOrFail($supplierId);
    }

    private function normalizeFields(): void
    {
        $this->phone = $this->phone ? BrazilianDocument::formatPhone($this->phone) : '';
        $this->document = BrazilianDocument::digitsOnly($this->document);
    }

    private function documentAlreadyExists(string $documentDigits, int $companyId, ?int $ignoreId = null): bool
    {
        return Supplier::query()
            ->where('company_id', $companyId)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->get()
            ->contains(fn (Supplier $existing) => BrazilianDocument::digitsOnly($existing->document) === $documentDigits);
    }

    public function render()
    {
        $suppliers = Supplier::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.suppliers.supplier-index', compact('suppliers'));
    }
}
