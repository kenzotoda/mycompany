<?php

namespace App\Livewire\Purchases;

use App\Livewire\Concerns\ManagesDocumentUploads;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\AttachmentService;
use App\Services\PurchaseService;
use App\Support\PaymentMethods;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class PurchaseForm extends Component
{
    use ManagesDocumentUploads;
    use WithFileUploads;

    public ?int $supplier_id = null;
    public string $purchase_date = '';
    public string $payment_method = PaymentMethods::PIX;
    public string $payment_status = 'pending';
    public string $notes = '';
    public array $items = [];

    public function mount(): void
    {
        $this->purchase_date = now()->toDateString();
        $this->items = [['supplier_id' => null, 'product_id' => null, 'quantity' => 1, 'unit_price' => 0]];
    }

    public function addItem(): void
    {
        $this->items[] = ['supplier_id' => $this->supplier_id, 'product_id' => null, 'quantity' => 1, 'unit_price' => 0];
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
            $this->items[$index]['unit_price'] = $product->purchase_price;
        }
    }

    public function save(PurchaseService $purchaseService, AttachmentService $attachmentService): void
    {
        $companyId = auth()->user()->company_id;

        $payload = [
            'supplier_id' => $this->supplier_id,
            'number' => 'COMP-'.now()->format('YmdHis'),
            'purchase_date' => $this->purchase_date,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
            'freight' => 0,
            'discount' => 0,
            'tax' => 0,
            'items' => $this->items,
        ];

        validator($payload, [
            'supplier_id' => ['required', Rule::exists('suppliers', 'id')->where(fn ($query) => $query->where('company_id', $companyId))],
            'number' => ['required', 'string', 'max:50'],
            'purchase_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:'.implode(',', PaymentMethods::values())],
            'payment_status' => ['required', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.supplier_id' => ['nullable', Rule::exists('suppliers', 'id')->where(fn ($query) => $query->where('company_id', $companyId))],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'gt:0'],
        ])->validate();

        foreach ($this->items as $index => $item) {
            $product = Product::query()
                ->where('company_id', $companyId)
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
        }

        $subtotal = collect($this->items)->sum(fn ($item) => ((float) $item['quantity']) * ((float) $item['unit_price']));

        $purchase = $purchaseService->create([
            ...$payload,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ], auth()->user());

        $attachmentService->attachToModel($purchase, $this->documentsForSave(), auth()->user(), 'purchase_document');

        session()->flash('status', 'Compra registrada com sucesso.');
        $this->redirectRoute('purchases.index');
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;

        return view('livewire.purchases.purchase-form', [
            'suppliers' => Supplier::where('company_id', $companyId)->orderBy('name')->get(),
            'products' => Product::where('company_id', $companyId)->orderBy('name')->get(),
        ]);
    }
}
