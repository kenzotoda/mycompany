<div class="space-y-6">
    <div class="mc-page-header">
        <div>
            <h1 class="mc-page-title">
                <i class="fa-solid fa-box mc-icon"></i>
                {{ $editing ? 'Editar produto' : 'Cadastrar produto' }}
            </h1>
            <p class="mc-page-subtitle">
                {{ $editing ? 'Atualize nome, SKU e estoque atual do produto' : 'Informe o nome e, se quiser, um código SKU' }}
            </p>
        </div>
        <a href="{{ route('products.index') }}" class="mc-btn-secondary">
            <i class="fa-solid fa-warehouse"></i> Ver estoque
        </a>
    </div>

    <form class="mc-card mc-form-section max-w-lg" novalidate x-on:keydown.enter.prevent="window.submitValidatedForm($el, () => $wire.save(), $wire)">
        <x-field label="Nome do produto" required>
            <input type="text" wire:model.live="name" class="mc-input" placeholder="Ex: Camiseta básica" required data-error-required="Informe o nome do produto.">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </x-field>
        <x-field label="SKU (opcional)">
            <input type="text" wire:model.live="sku" class="mc-input" placeholder="Gerado automaticamente se vazio">
            @error('sku') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            <p class="mt-1.5 mc-hint">Código interno para identificar o produto no estoque.</p>
        </x-field>

        @if ($editing)
            <x-field label="Estoque atual" required>
                <x-numeric-input
                    :decimal="false"
                    wire:model.live="stock_quantity"
                    placeholder="0"
                    required
                    data-numeric-integer
                    data-validate-min="0"
                    data-error-required="Informe o estoque atual do produto."
                    data-error-min="O estoque não pode ser negativo."
                />
                @error('stock_quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1.5 mc-hint">Este campo só pode ser alterado na edição.</p>
            </x-field>
        @endif

        <button
            type="button"
            wire:loading.attr="disabled"
            x-on:click="window.submitValidatedForm($el.closest('form'), () => $wire.save(), $wire)"
            class="mc-btn-primary"
        >
            <i class="fa-solid fa-floppy-disk"></i>
            {{ $editing ? 'Salvar alterações' : 'Salvar produto' }}
        </button>
    </form>
</div>
