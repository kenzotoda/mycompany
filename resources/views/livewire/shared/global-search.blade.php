<div class="relative" x-data="{ open: false }">
    <div class="relative">
        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-brand-muted"></i>
        <input
            type="text"
            wire:model.live.debounce.400ms="query"
            @focus="open = true"
            @click.outside="open = false"
            placeholder="Pesquisar produtos, clientes e fornecedores..."
            class="mc-input !mt-0 pl-10"
        />
    </div>

    @if (mb_strlen(trim($query)) >= 2)
        <div x-show="open" class="absolute z-20 mt-2 w-full rounded-xl border border-brand-border bg-white p-4 shadow-card">
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-brand-muted">Produtos</p>
            @forelse ($products as $item)
                <p class="text-sm text-brand-black"><i class="fa-solid fa-box mr-2 text-brand-orange"></i>{{ $item->name }}</p>
            @empty
                <p class="text-xs text-brand-muted">Sem resultados</p>
            @endforelse

            <p class="mb-2 mt-4 text-xs font-semibold uppercase tracking-wide text-brand-muted">Clientes</p>
            @forelse ($customers as $item)
                <p class="text-sm text-brand-black"><i class="fa-solid fa-user mr-2 text-brand-orange"></i>{{ $item->name }}</p>
            @empty
                <p class="text-xs text-brand-muted">Sem resultados</p>
            @endforelse

            <p class="mb-2 mt-4 text-xs font-semibold uppercase tracking-wide text-brand-muted">Fornecedores</p>
            @forelse ($suppliers as $item)
                <p class="text-sm text-brand-black"><i class="fa-solid fa-truck mr-2 text-brand-orange"></i>{{ $item->name }}</p>
            @empty
                <p class="text-xs text-brand-muted">Sem resultados</p>
            @endforelse
        </div>
    @endif
</div>
