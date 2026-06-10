<div class="space-y-6">
    <div class="mc-page-header">
        <div>
            <h1 class="mc-page-title"><i class="fa-solid fa-receipt mc-icon"></i> Nova venda</h1>
            <p class="mc-page-subtitle">Registre uma venda e atualize o estoque</p>
        </div>
        <a href="{{ route('sales.index') }}" class="mc-btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>

    <form class="mc-card mc-form-section" data-validate-sale-form novalidate x-on:keydown.enter.prevent="window.submitValidatedForm($el, () => $wire.save(), $wire)">
        <x-field label="Cliente" required>
            <x-searchable-select wire:model="customer_id" required data-error-required="Selecione o cliente que comprou.">
                <option value="">Selecione o cliente que comprou</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </x-searchable-select>
            @error('customer_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            @if ($customers->isEmpty())
                <p class="mt-2 mc-hint">Nenhum cliente cadastrado. <a href="{{ route('customers.index') }}" class="mc-link">Cadastre em Clientes</a>.</p>
            @endif
        </x-field>

        <div class="mc-form-grid">
            <x-field label="Data da venda">
                <input type="date" wire:model="sale_date" class="mc-input" required data-error-required="Informe a data da venda.">
                @error('sale_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
            <x-field label="Forma de pagamento">
                <x-payment-method-select wire:model.live="payment_method" />
                @error('payment_method') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
            @if ($payment_method === \App\Support\PaymentMethods::CREDITO)
                <x-field label="Parcelas" required>
                    <x-numeric-input wire:model="installments" :decimal="false" placeholder="Ex: 3" required data-validate-installments data-validate-min="2" data-numeric-integer data-error-required="Informe em quantas parcelas será o crédito." data-error-min="O crédito parcelado deve ter no mínimo 2 parcelas." />
                    @error('installments') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </x-field>
            @endif
        </div>

        <div class="space-y-3">
            <h3 class="mc-card-title"><i class="fa-solid fa-list mr-2 text-brand-orange"></i>Itens da venda</h3>

            @if ($products->isEmpty())
                <div class="mc-alert-warning">
                    Nenhum produto cadastrado. <a href="{{ route('products.create') }}" class="mc-link">Cadastre em Produtos</a> antes de registrar a venda.
                </div>
            @endif

            @foreach ($items as $index => $item)
                <div class="mc-item-row md:grid-cols-4">
                    <x-field label="Fornecedor do item">
                        <x-searchable-select wire:model.live="items.{{ $index }}.supplier_id" wire:key="sale-item-supplier-{{ $index }}">
                            <option value="">Sem fornecedor</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </x-searchable-select>
                        @error('items.'.$index.'.supplier_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </x-field>
                    <x-field label="Produto" required>
                        <x-searchable-select wire:model.live="items.{{ $index }}.product_id" wire:key="sale-product-{{ $index }}" required data-error-required="Selecione o produto.">
                            <option value="">Selecione</option>
                            @foreach ($products->where('supplier_id', data_get($item, 'supplier_id') ?: null) as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }} (estoque: {{ $product->formattedStock() }})
                                </option>
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

        @include('livewire.shared.document-upload', ['label' => 'Anexar documentos (nota fiscal, comprovante, recibo...)'])

        <button
            type="button"
            wire:loading.attr="disabled"
            x-on:click="window.submitValidatedForm($el.closest('form'), () => $wire.save(), $wire)"
            class="mc-btn-primary"
        >
            <i class="fa-solid fa-floppy-disk"></i> Salvar venda
        </button>
    </form>
</div>
