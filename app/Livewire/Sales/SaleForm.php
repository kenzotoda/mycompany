<?php

namespace App\Livewire\Sales;

use App\Livewire\Concerns\ManagesDocumentUploads;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\AttachmentService;
use App\Services\SaleService;
use App\Support\PaymentMethods;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class SaleForm extends Component
{
    use ManagesDocumentUploads;
    use WithFileUploads;

    public ?int $customer_id = null;
    public string $sale_date = '';
    public string $payment_method = PaymentMethods::PIX;
    public string $payment_status = 'pending';
    public bool $is_installment = false;
    public ?int $installments = null;
    public string $notes = '';
    public array $items = [];

    public function mount(): void
    {
        $this->sale_date = now()->toDateString();
        $this->items = [['supplier_id' => null, 'product_id' => null, 'quantity' => 1, 'unit_price' => 0]];
    }

    public function addItem(): void
    {
        $this->items[] = ['supplier_id' => null, 'product_id' => null, 'quantity' => 1, 'unit_price' => 0];
    }

    public function updatedPaymentMethod(string $value): void
    {
        if ($value !== PaymentMethods::CREDITO) {
            $this->is_installment = false;
            $this->installments = null;
            $this->resetValidation('installments');
        }
    }

    public function updatedItems(mixed $value, string $key): void
    {
        $segments = explode('.', $key);

        if (count($segments) !== 2) {
            return;
        }

        $index = (int) $segments[0];
        $field = $segments[1];

        if ($field === 'supplier_id') {
            $this->items[$index]['product_id'] = null;
            $this->items[$index]['unit_price'] = 0;
            $this->resetValidation("items.$index.product_id");
            $this->resetValidation("items.$index.unit_price");

            return;
        }

        if ($field !== 'product_id' || ! $value) {
            return;
        }

        $product = Product::query()
            ->where('company_id', auth()->user()->company_id)
            ->find($value);

        if ($product) {
            $this->items[$index]['unit_price'] = $product->sale_price;
        }
    }

    public function save(SaleService $saleService, AttachmentService $attachmentService): void
    {
        $companyId = auth()->user()->company_id;
        $isCredit = $this->payment_method === PaymentMethods::CREDITO;

        $payload = [
            'customer_id' => $this->customer_id,
            'number' => 'VEND-'.now()->format('YmdHis'),
            'sale_date' => $this->sale_date,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'is_installment' => $isCredit,
            'installments' => $isCredit ? $this->installments : null,
            'due_date' => null,
            'notes' => $this->notes,
            'freight' => 0,
            'discount' => 0,
            'tax' => 0,
            'items' => $this->items,
        ];

        validator($payload, [
            'customer_id' => ['required', 'exists:customers,id'],
            'number' => ['required', 'string', 'max:50'],
            'sale_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:'.implode(',', PaymentMethods::values())],
            'payment_status' => ['required', 'string', 'max:50'],
            'installments' => [$isCredit ? 'required' : 'nullable', 'integer', 'min:2', 'max:24'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.supplier_id' => ['nullable', Rule::exists('suppliers', 'id')->where(fn ($query) => $query->where('company_id', $companyId))],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'gt:0'],
        ], [
            'installments.required' => 'Informe em quantas parcelas será o crédito.',
            'installments.min' => 'O crédito parcelado deve ter no mínimo 2 parcelas.',
        ])->validate();

        foreach ($this->items as $index => $item) {
            $product = Product::query()
                ->where('company_id', auth()->user()->company_id)
                ->find($item['product_id']);

            if (! $product) {
                continue;
            }

            $itemSupplierId = $item['supplier_id'] ? (int) $item['supplier_id'] : null;
            $productSupplierId = $product->supplier_id ? (int) $product->supplier_id : null;

            if ($itemSupplierId !== $productSupplierId) {
                $this->addError(
                    "items.$index.product_id",
                    'Selecione um produto do fornecedor informado neste item.'
                );

                return;
            }

            if ((int) $item['quantity'] > $product->stockUnits()) {
                $this->addError(
                    "items.$index.quantity",
                    'Estoque insuficiente. Disponível: '.$product->formattedStock()
                );

                return;
            }
        }

        $subtotal = collect($this->items)->sum(fn ($item) => ((float) $item['quantity']) * ((float) $item['unit_price']));

        $sale = $saleService->create([
            ...$payload,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ], auth()->user());

        $attachmentService->attachToModel($sale, $this->documentsForSave(), auth()->user(), 'sale_document');

        session()->flash('status', 'Venda registrada com sucesso.');
        $this->redirectRoute('sales.index', navigate: true);
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;

        return view('livewire.sales.sale-form', [
            'customers' => Customer::where('company_id', $companyId)->orderBy('name')->get(),
            'suppliers' => Supplier::where('company_id', $companyId)->orderBy('name')->get(),
            'products' => Product::where('company_id', $companyId)->orderBy('name')->get(),
        ]);
    }
}
