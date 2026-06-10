<?php

namespace App\Livewire\Sales;

use App\Livewire\Concerns\ManagesDocumentUploads;
use App\Models\Customer;
use App\Models\Product;
use App\Services\AttachmentService;
use App\Services\SaleService;
use App\Support\PaymentMethods;
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
        $this->items = [['product_id' => null, 'quantity' => 1, 'unit_price' => 0]];
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => null, 'quantity' => 1, 'unit_price' => 0];
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
        if (! str_ends_with($key, 'product_id') || ! $value) {
            return;
        }

        $index = (int) explode('.', $key)[0];
        $product = Product::find($value);

        if ($product) {
            $this->items[$index]['unit_price'] = $product->sale_price;
        }
    }

    public function save(SaleService $saleService, AttachmentService $attachmentService): void
    {
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
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'gt:0'],
        ], [
            'installments.required' => 'Informe em quantas parcelas será o crédito.',
            'installments.min' => 'O crédito parcelado deve ter no mínimo 2 parcelas.',
        ])->validate();

        foreach ($this->items as $index => $item) {
            $product = Product::find($item['product_id']);

            if ($product && (int) $item['quantity'] > $product->stockUnits()) {
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
            'products' => Product::where('company_id', $companyId)->orderBy('name')->get(),
        ]);
    }
}
