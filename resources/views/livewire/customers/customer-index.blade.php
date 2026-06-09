<div class="space-y-6">
    <div class="mc-page-header">
        <div>
            <h1 class="mc-page-title"><i class="fa-solid fa-users mc-icon"></i> Clientes</h1>
            <p class="mc-page-subtitle">Cadastre quem compra da sua empresa</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mc-alert-success"><i class="fa-solid fa-circle-check mr-2"></i>{{ session('status') }}</div>
    @endif

    <form wire:submit="save" class="mc-card mc-form-section">
        <h3 class="mc-card-title"><i class="fa-solid fa-user-plus mr-2 text-brand-orange"></i>Cadastrar cliente</h3>

        <div class="mc-form-grid">
            <x-field label="Nome do cliente" required class="md:col-span-2">
                <input type="text" wire:model="name" class="mc-input" placeholder="Nome completo ou razão social">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>

            <div class="mc-field md:col-span-2">
                <span class="mc-label">Documento <span class="font-normal text-brand-muted">(opcional)</span></span>
                <div class="mt-1.5 flex flex-wrap items-center gap-4">
                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm">
                        <input type="radio" name="customer_document_type" value="cpf" @checked($document_type === 'cpf') wire:click="setDocumentType('cpf')" class="text-brand-orange focus:ring-brand-orange">
                        CPF
                    </label>
                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm">
                        <input type="radio" name="customer_document_type" value="cnpj" @checked($document_type === 'cnpj') wire:click="setDocumentType('cnpj')" class="text-brand-orange focus:ring-brand-orange">
                        CNPJ
                    </label>
                    @if ($document_type !== '')
                        <button type="button" wire:click="clearDocumentType" class="mc-hint hover:text-brand-orange">
                            <i class="fa-solid fa-xmark mr-1"></i>Limpar seleção
                        </button>
                    @endif
                </div>
                @error('document_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            @if ($document_type === 'cpf')
                <x-field label="CPF" class="md:col-span-2" wire:key="customer-document-cpf">
                    <x-masked-input mask="cpf" wire:model="document" maxlength="14" placeholder="000.000.000-00" />
                    @error('document') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </x-field>
            @elseif ($document_type === 'cnpj')
                <x-field label="CNPJ" class="md:col-span-2" wire:key="customer-document-cnpj">
                    <x-masked-input mask="cnpj" wire:model="document" maxlength="18" placeholder="00.000.000/0000-00" />
                    @error('document') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </x-field>
            @endif

            <x-field label="Telefone">
                <x-masked-input mask="phone" wire:model="phone" maxlength="15" placeholder="(11) 99999-9999" />
                @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
            <x-field label="E-mail">
                <input type="email" wire:model="email" class="mc-input" placeholder="cliente@email.com">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
        </div>

        <button type="submit" class="mc-btn-primary">
            <i class="fa-solid fa-floppy-disk"></i> Salvar cliente
        </button>
    </form>

    <div class="mc-table-wrap">
        <table class="mc-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Documento</th>
                    <th>Telefone</th>
                    <th>E-mail</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td class="font-medium">{{ $customer->name }}</td>
                        <td>{{ $customer->document ?? '-' }}</td>
                        <td>{{ $customer->phone ?? '-' }}</td>
                        <td>{{ $customer->email ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-12 text-center text-brand-muted">Nenhum cliente cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $customers->links() }}
</div>
