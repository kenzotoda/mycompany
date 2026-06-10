<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\User;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(
        private readonly PurchaseRepositoryInterface $purchaseRepository,
        private readonly InventoryService $inventoryService,
    ) {
    }

    public function create(array $payload, User $user): Purchase
    {
        return DB::transaction(function () use ($payload, $user): Purchase {
            $purchase = $this->purchaseRepository->create([
                ...$payload,
                'company_id' => $user->company_id,
                'created_by' => $user->id,
            ]);

            foreach ($payload['items'] as $itemData) {
                $quantity = (int) $itemData['quantity'];

                $item = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $itemData['unit_price'],
                    'total' => $quantity * $itemData['unit_price'],
                ]);

                $this->inventoryService->increaseFromPurchase($item->product, $quantity, $purchase);
            }

            return $purchase->load('items.product');
        });
    }
}
