<?php

namespace App\Models;

use App\Support\BrazilianNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'category_id',
        'name',
        'sku',
        'description',
        'purchase_price',
        'sale_price',
        'stock_quantity',
        'min_stock',
        'is_active',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockUnits(): int
    {
        return BrazilianNumber::toInteger($this->stock_quantity);
    }

    public function formattedStock(): string
    {
        return BrazilianNumber::formatInteger($this->stock_quantity);
    }

    public function hasLowStock(): bool
    {
        return $this->stockUnits() < 1;
    }
}
