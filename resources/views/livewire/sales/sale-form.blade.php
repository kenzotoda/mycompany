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

    <form wire:submit="save" class="mc-card mc-form-section">
        <x-field label="Cliente" required>
            <select wire:model="customer_id" class="mc-input">
                <option value="">Selecione o cliente que comprou</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            @error('customer_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            @if ($customers->isEmpty())
                <p class="mt-2 mc-hint">Nenhum cliente cadastrado. <a href="{{ route('customers.index') }}" class="mc-link">Cadastre em Clientes</a>.</p>
            @endif
        </x-field>

        <div class="mc-form-grid">
            <x-field label="Data da venda">
                <input type="date" wire:model="sale_date" class="mc-input">
                @error('sale_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
            <x-field label="Forma de pagamento">
                <x-payment-method-select wire:model.live="payment_method" />
                @error('payment_method') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
            @if ($payment_method === \App\Support\PaymentMethods::CREDITO)
                <x-field label="Parcelas" required>
                    <x-numeric-input wire:model="installments" :decimal="false" placeholder="Ex: 3" />
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
                <div class="mc-item-row">
                    <x-field label="Produto" required>
                        <select wire:model.live="items.{{ $index }}.product_id" class="mc-input">
                            <option value="">Selecione</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }} (estoque: {{ number_format($product->stock_quantity, 3, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('items.'.$index.'.product_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </x-field>
                    <x-field label="Quantidade" required>
                        <x-numeric-input wire:model="items.{{ $index }}.quantity" placeholder="0" />
                        @error('items.'.$index.'.quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </x-field>
                    <x-field label="Valor unitário" required>
                        <x-numeric-input wire:model="items.{{ $index }}.unit_price" placeholder="0,00" />
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

        <button type="submit" class="mc-btn-primary">
            <i class="fa-solid fa-floppy-disk"></i> Salvar venda
        </button>
    </form>
</div>
