<div class="space-y-6">
    <div class="mc-page-header">
        <div>
            <h1 class="mc-page-title"><i class="fa-solid fa-cart-plus mc-icon"></i> Nova compra</h1>
            <p class="mc-page-subtitle">Registre uma nova entrada de produtos</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="mc-btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>

    <form class="mc-card mc-form-section" novalidate x-on:keydown.enter.prevent="window.submitValidatedForm($el, () => $wire.save(), $wire)">
        <x-field label="Fornecedor" required>
            <x-searchable-select wire:model="supplier_id" required data-error-required="Selecione um fornecedor.">
                <option value="">Selecione um fornecedor</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </x-searchable-select>
            @error('supplier_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            @if ($suppliers->isEmpty())
                <p class="mt-2 mc-hint">Nenhum fornecedor cadastrado. <a href="{{ route('suppliers.index') }}" class="mc-link">Cadastre em Fornecedores</a>.</p>
            @endif
        </x-field>

        <div class="mc-form-grid">
            <x-field label="Data da compra">
                <input type="date" wire:model="purchase_date" class="mc-input" required data-error-required="Informe a data da compra.">
                @error('purchase_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
            <x-field label="Forma de pagamento">
                <x-payment-method-select wire:model="payment_method" />
                @error('payment_method') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
        </div>

        <div class="space-y-3">
            <h3 class="mc-card-title"><i class="fa-solid fa-list mr-2 text-brand-orange"></i>Itens da compra</h3>

            @if ($products->isEmpty())
                <div class="mc-alert-warning">
                    Nenhum produto cadastrado. <a href="{{ route('products.create') }}" class="mc-link">Cadastre em Produtos</a> antes de registrar a compra.
                </div>
            @endif

            @foreach ($items as $index => $item)
                <div class="mc-item-row">
                    <x-field label="Produto" required>
                        <x-searchable-select wire:model="items.{{ $index }}.product_id" wire:key="purchase-product-{{ $index }}" required data-error-required="Selecione o produto.">
                            <option value="">Selecione</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </x-searchable-select>
                        @error('items.'.$index.'.product_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </x-field>
                    <x-field label="Quantidade" required>
                        <x-numeric-input :decimal="false" wire:model="items.{{ $index }}.quantity" placeholder="0" required data-validate-min="1" data-numeric-integer data-error-required="Informe a quantidade." data-error-min="A quantidade deve ser no mínimo 1." />
                        @error('items.'.$index.'.quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </x-field>
                    <x-field label="Valor unitário" required>
                        <x-numeric-input wire:model="items.{{ $index }}.unit_price" placeholder="0,00" required data-validate-gt-zero data-error-required="Informe o valor unitário." />
                        @error('items.'.$index.'.unit_price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </x-field>
                </div>
            @endforeach

            <button type="button" wire:click="addItem" class="mc-link text-sm">
                <i class="fa-solid fa-plus mr-1"></i> Adicionar item
            </button>
        </div>

        <x-field label="Observações">
            <textarea wire:model="notes" rows="2" class="mc-input"></textarea>
        </x-field>

        @include('livewire.shared.document-upload', ['label' => 'Anexar documentos (nota fiscal, boleto, comprovante...)'])

        <button
            type="button"
            wire:loading.attr="disabled"
            x-on:click="window.submitValidatedForm($el.closest('form'), () => $wire.save(), $wire)"
            class="mc-btn-primary"
        >
            <i class="fa-solid fa-floppy-disk"></i> Salvar compra
        </button>
    </form>
</div>
