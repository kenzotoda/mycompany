<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
    <div class="mc-stat-card">
        <div class="flex items-center justify-between">
            <span class="mc-stat-label">Total de compras</span>
            <i class="fa-solid fa-cart-shopping text-brand-orange"></i>
        </div>
        <p class="mc-stat-value">{{ (int) ($metrics['total_compras'] ?? 0) }}</p>
    </div>
    <div class="mc-stat-card">
        <div class="flex items-center justify-between">
            <span class="mc-stat-label">Total de vendas</span>
            <i class="fa-solid fa-cash-register text-brand-orange"></i>
        </div>
        <p class="mc-stat-value">{{ (int) ($metrics['total_vendas'] ?? 0) }}</p>
    </div>
    <div class="mc-stat-card">
        <div class="flex items-center justify-between">
            <span class="mc-stat-label">Sem estoque</span>
            <i class="fa-solid fa-triangle-exclamation text-brand-orange"></i>
        </div>
        <p class="mc-stat-value text-brand-orange">{{ (int) ($metrics['baixo_estoque'] ?? 0) }}</p>
    </div>
</div>
