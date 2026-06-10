<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Support\BrazilianDocument;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerIndex extends Component
{
    use WithPagination;

    public ?int $editingId = null;

    public string $name = '';

    public string $document_type = '';

    public string $document = '';

    public string $phone = '';

    public string $email = '';

    public function edit(int $customerId): void
    {
        $customer = $this->findCustomer($customerId);

        $this->editingId = $customer->id;
        $this->name = $customer->name;
        $this->document_type = $this->resolveDocumentType($customer->document);
        $this->document = $customer->document ?? '';
        $this->phone = $customer->phone ?? '';
        $this->email = $customer->email ?? '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->reset('name', 'document_type', 'document', 'phone', 'email');
        $this->resetValidation();
    }

    public function setDocumentType(string $type): void
    {
        if (! in_array($type, ['cpf', 'cnpj'], true)) {
            return;
        }

        if ($this->document_type === $type) {
            return;
        }

        $this->document_type = $type;
        $this->document = '';
        $this->resetValidation('document', 'document_type');
    }

    public function clearDocumentType(): void
    {
        $this->document_type = '';
        $this->document = '';
        $this->resetValidation('document', 'document_type');
    }

    public function save(): void
    {
        $companyId = auth()->user()->company_id;

        if (! $companyId) {
            $this->addError('name', 'Seu usuário não está vinculado a uma empresa.');

            return;
        }

        $customer = $this->editingId ? $this->findCustomer($this->editingId) : null;

        $this->normalizeFields();

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'document_type' => ['nullable', 'in:cpf,cnpj'],
            'document' => [
                'nullable',
                'string',
                'max:18',
                Rule::unique('customers', 'document')
                    ->where(fn ($query) => $query->where('company_id', $companyId))
                    ->ignore($customer?->id),
            ],
            'phone' => ['nullable', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:255'],
        ], [
            'name.required' => 'Informe o nome do cliente.',
            'document.unique' => 'Este documento já está cadastrado para outro cliente.',
            'email.email' => 'Informe um e-mail válido.',
        ]);

        if ($data['document_type'] && blank($data['document'])) {
            $this->addError('document', 'Informe o '.strtoupper($data['document_type']).' ou remova o tipo selecionado.');

            return;
        }

        if (! $data['document_type'] && filled($data['document'])) {
            $this->addError('document_type', 'Selecione CPF ou CNPJ para informar o documento.');

            return;
        }

        if ($data['document_type'] === 'cpf' && filled($data['document']) && ! BrazilianDocument::isCompleteCpf($data['document'])) {
            $this->addError('document', 'O CPF deve ter 11 dígitos no formato 000.000.000-00.');

            return;
        }

        if ($data['document_type'] === 'cnpj' && filled($data['document']) && ! BrazilianDocument::isCompleteCnpj($data['document'])) {
            $this->addError('document', 'O CNPJ deve ter 14 dígitos no formato 00.000.000/0000-00.');

            return;
        }

        if ($data['phone'] && ! BrazilianDocument::isCompletePhone($data['phone'])) {
            $this->addError('phone', 'Informe um telefone válido com DDD.');

            return;
        }

        $payload = [
            'name' => $data['name'],
            'document' => $data['document'] ?: null,
            'phone' => $data['phone'] ?: null,
            'email' => $data['email'] ?: null,
        ];

        if ($customer) {
            $customer->update($payload);
            session()->flash('status', 'Cliente atualizado com sucesso.');
        } else {
            Customer::create([
                'company_id' => $companyId,
                ...$payload,
            ]);
            session()->flash('status', 'Cliente cadastrado com sucesso.');
        }

        $this->cancelEdit();
    }

    public function delete(int $customerId): void
    {
        $customer = $this->findCustomer($customerId);

        if ($this->editingId === $customer->id) {
            $this->cancelEdit();
        }

        $customer->delete();

        session()->flash('status', 'Cliente excluído com sucesso.');
    }

    private function findCustomer(int $customerId): Customer
    {
        return Customer::query()
            ->where('company_id', auth()->user()->company_id)
            ->findOrFail($customerId);
    }

    private function normalizeFields(): void
    {
        $this->phone = $this->phone ? BrazilianDocument::formatPhone($this->phone) : '';

        $this->document = match ($this->document_type) {
            'cpf' => $this->document ? BrazilianDocument::formatCpf($this->document) : '',
            'cnpj' => $this->document ? BrazilianDocument::formatCnpj($this->document) : '',
            default => '',
        };
    }

    private function resolveDocumentType(?string $document): string
    {
        if (blank($document)) {
            return '';
        }

        $digits = strlen(BrazilianDocument::digitsOnly($document));

        return match ($digits) {
            14 => 'cnpj',
            11 => 'cpf',
            default => '',
        };
    }

    public function render()
    {
        $customers = Customer::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.customers.customer-index', compact('customers'));
    }
}
