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

    @if (session('status'))
        <div class="mc-alert-success"><i class="fa-solid fa-circle-check mr-2"></i>{{ session('status') }}</div>
    @endif

    <div class="mc-alert-warning">
        <i class="fa-solid fa-arrows-rotate mr-2"></i>
        Entradas pelas <a href="{{ route('purchases.index') }}" class="mc-link">Compras</a> e saídas pelas <a href="{{ route('sales.index') }}" class="mc-link">Vendas</a>.
        Alerta de estoque baixo só aparece quando a quantidade chega a <strong>zero</strong>.
    </div>

    <div class="mc-card-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-md">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-brand-muted"></i>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="query"
                    placeholder="Pesquisar por produto, SKU{{ $hasSupplierColumn ? ' ou fornecedor' : '' }}..."
                    class="mc-input !mt-0 pl-10"
                    autocomplete="off"
                />
            </div>
            <p class="text-sm text-brand-muted" wire:loading.remove wire:target="query">
                Mostrando {{ $products->count() }} de {{ $products->total() }} produto(s)
            </p>
            <p class="text-sm text-brand-orange" wire:loading wire:target="query">
                <i class="fa-solid fa-spinner fa-spin mr-1"></i> Pesquisando...
            </p>
        </div>
    </div>

    <div class="mc-table-wrap">
        <table class="mc-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th class="w-28">SKU</th>
                    <th>Fornecedor</th>
                    <th class="mc-col-right w-28">Estoque</th>
                    <th class="mc-col-center">Status</th>
                    <th class="mc-col-actions">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr wire:key="product-row-{{ $product->id }}" class="{{ $product->hasLowStock() ? 'bg-amber-50/60' : '' }}">
                        <td class="font-medium">{{ $product->name }}</td>
                        <td class="text-brand-muted">{{ $product->sku }}</td>
                        <td>{{ $product->supplier?->name ?? 'Sem fornecedor' }}</td>
                        <td class="mc-col-right font-semibold">{{ $product->formattedStock() }}</td>
                        <td class="mc-col-center">
                            @if ($product->hasLowStock())
                                <span class="mc-badge-warning"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Sem estoque</span>
                            @else
                                <span class="mc-badge-success"><i class="fa-solid fa-check mr-1"></i>OK</span>
                            @endif
                        </td>
                        <td class="mc-col-actions">
                            <div class="mc-table-actions">
                                <a href="{{ route('products.edit', $product) }}" class="mc-btn-icon" title="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button
                                    type="button"
                                    wire:key="product-delete-{{ $product->id }}"
                                    wire:click="delete({{ $product->id }})"
                                    wire:confirm="Excluir o produto &quot;{{ $product->name }}&quot;?"
                                    wire:loading.attr="disabled"
                                    class="mc-btn-icon-danger"
                                    title="Excluir"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-brand-muted">
                            @if (trim($query) !== '')
                                Nenhum produto encontrado para "{{ $query }}".
                            @else
                                Nenhum produto cadastrado.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $products->links() }}
</div>
