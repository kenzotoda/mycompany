<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Support\BrazilianDocument;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerIndex extends Component
{
    use WithPagination;

    public string $name = '';
    public string $document_type = '';
    public string $document = '';
    public string $phone = '';
    public string $email = '';

    public function setDocumentType(string $type): void
    {
        if (! in_array($type, ['cpf', 'cnpj'], true)) {
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
        $this->normalizeFields();

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'document_type' => ['nullable', 'in:cpf,cnpj'],
            'document' => ['nullable', 'string', 'max:18'],
            'phone' => ['nullable', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:255'],
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

        Customer::create([
            'company_id' => auth()->user()->company_id,
            'name' => $data['name'],
            'document' => $data['document'] ?: null,
            'phone' => $data['phone'] ?: null,
            'email' => $data['email'] ?: null,
        ]);

        $this->reset('name', 'document_type', 'document', 'phone', 'email');
        session()->flash('status', 'Cliente cadastrado com sucesso.');
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

    public function render()
    {
        $customers = Customer::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.customers.customer-index', compact('customers'));
    }
}
