<div class="space-y-6">
    <div class="mc-page-header">
        <div>
            <h1 class="mc-page-title"><i class="fa-solid fa-warehouse mc-icon"></i> Estoque</h1>
            <p class="mc-page-subtitle">Atualizado automaticamente por compras e vendas</p>
        </div>
        <a href="{{ route('products.create') }}" class="mc-btn-primary">
            <i class="fa-solid fa-plus"></i> Novo produto
        </a>
    </div>

    <div class="mc-alert-warning">
        <i class="fa-solid fa-arrows-rotate mr-2"></i>
        Entradas pelas <a href="{{ route('purchases.index') }}" class="mc-link">Compras</a> e saídas pelas <a href="{{ route('sales.index') }}" class="mc-link">Vendas</a>.
        Alerta de estoque baixo só aparece quando a quantidade chega a <strong>zero</strong>.
    </div>

    <div class="mc-table-wrap">
        <table class="mc-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>SKU</th>
                    <th class="text-right">Estoque</th>
                    <th class="text-right">Compra</th>
                    <th class="text-right">Venda</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="{{ $product->hasLowStock() ? 'bg-amber-50/60' : '' }}">
                        <td class="font-medium">{{ $product->name }}</td>
                        <td class="text-brand-muted">{{ $product->sku }}</td>
                        <td class="text-right font-semibold">{{ number_format($product->stock_quantity, 3, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($product->purchase_price, 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</td>
                        <td class="text-center">
                            @if ($product->hasLowStock())
                                <span class="mc-badge-warning"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Sem estoque</span>
                            @else
                                <span class="mc-badge-success"><i class="fa-solid fa-check mr-1"></i>OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-12 text-center text-brand-muted">Nenhum produto cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $products->links() }}
</div>
