<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    public function create(array $payload, User $user): Sale
    {
        return DB::transaction(function () use ($payload, $user): Sale {
            $sale = Sale::create([
                ...$payload,
                'company_id' => $user->company_id,
                'created_by' => $user->id,
            ]);

            foreach ($payload['items'] as $itemData) {
                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);

                if ((float) $itemData['quantity'] > (float) $product->stock_quantity) {
                    throw new \InvalidArgumentException(
                        "Estoque insuficiente para {$product->name}."
                    );
                }

                $item = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total' => $itemData['quantity'] * $itemData['unit_price'],
                ]);

                $this->inventoryService->decreaseFromSale($product, (float) $item->quantity, $sale);
            }

            return $sale->load('items.product');
        });
    }
}
