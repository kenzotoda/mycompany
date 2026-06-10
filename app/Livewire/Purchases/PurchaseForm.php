<?php

namespace App\Livewire\Purchases;

use App\Livewire\Concerns\ManagesDocumentUploads;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\AttachmentService;
use App\Services\PurchaseService;
use App\Support\PaymentMethods;
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
        $this->items = [['product_id' => null, 'quantity' => 1, 'unit_price' => 0]];
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => null, 'quantity' => 1, 'unit_price' => 0];
    }

    public function save(PurchaseService $purchaseService, AttachmentService $attachmentService): void
    {
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
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'number' => ['required', 'string', 'max:50'],
            'purchase_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:'.implode(',', PaymentMethods::values())],
            'payment_status' => ['required', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'gt:0'],
        ])->validate();

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
