<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;

class InventoryService
{
    public function increaseFromPurchase(Product $product, int $quantity, object $reference): void
    {
        $product->increment('stock_quantity', $quantity);

        StockMovement::create([
            'company_id' => $product->company_id,
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => $quantity,
            'reference_type' => $reference::class,
            'reference_id' => $reference->id,
            'notes' => 'Entrada por compra',
        ]);
    }

    public function decreaseFromSale(Product $product, int $quantity, object $reference): void
    {
        $product->decrement('stock_quantity', $quantity);

        StockMovement::create([
            'company_id' => $product->company_id,
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => $quantity,
            'reference_type' => $reference::class,
            'reference_id' => $reference->id,
            'notes' => 'Saida por venda',
        ]);
    }
}
