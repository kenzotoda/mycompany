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

    <form id="customer-form" class="mc-card mc-form-section" wire:key="customer-form-{{ $editingId ?? 'create' }}-{{ $document_type ?: 'none' }}" data-validate-customer-document novalidate x-on:keydown.enter.prevent="window.submitValidatedForm($el, () => $wire.save())">
        <h3 class="mc-card-title">
            <i class="fa-solid {{ $editingId ? 'fa-pen-to-square' : 'fa-user-plus' }} mr-2 text-brand-orange"></i>
            {{ $editingId ? 'Editar cliente' : 'Cadastrar cliente' }}
        </h3>

        <div class="mc-form-grid">
            <x-field label="Nome do cliente" required class="md:col-span-2">
                <input type="text" wire:model.live="name" class="mc-input" placeholder="Nome completo ou razão social" required data-error-required="Informe o nome do cliente.">
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
                <p class="mc-field-error mt-1 hidden text-xs text-red-600" data-frontend-error role="alert"></p>
            </div>

            @if ($document_type === 'cpf')
                <x-field label="CPF" class="md:col-span-2" wire:key="customer-document-cpf-{{ $editingId ?? 'new' }}">
                    <x-masked-input mask="cpf" wire:model.live="document" maxlength="14" placeholder="000.000.000-00" required data-validate-document data-validate-cpf />
                    @error('document') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </x-field>
            @elseif ($document_type === 'cnpj')
                <x-field label="CNPJ" class="md:col-span-2" wire:key="customer-document-cnpj-{{ $editingId ?? 'new' }}">
                    <x-masked-input mask="cnpj" wire:model.live="document" maxlength="18" placeholder="00.000.000/0000-00" required data-validate-document data-validate-cnpj />
                    @error('document') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </x-field>
            @endif

            <x-field label="Telefone">
                <x-masked-input mask="phone" wire:model.live="phone" maxlength="15" placeholder="(11) 99999-9999" data-validate-phone-if-filled />
                @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
            <x-field label="E-mail">
                <input type="email" wire:model.live="email" class="mc-input" placeholder="cliente@email.com">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button
                type="button"
                wire:loading.attr="disabled"
                x-on:click="window.submitValidatedForm($el.closest('form'), () => $wire.save())"
                class="mc-btn-primary"
            >
                <i class="fa-solid fa-floppy-disk"></i>
                {{ $editingId ? 'Salvar alterações' : 'Salvar cliente' }}
            </button>
            @if ($editingId)
                <button type="button" wire:click="cancelEdit" class="mc-btn-secondary">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </button>
            @endif
        </div>
    </form>

    <div class="mc-table-wrap">
        <table class="mc-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th class="w-40">Documento</th>
                    <th class="w-36">Telefone</th>
                    <th>E-mail</th>
                    <th class="mc-col-actions">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr class="{{ $editingId === $customer->id ? 'bg-orange-50/60' : '' }}">
                        <td class="font-medium">{{ $customer->name }}</td>
                        <td class="whitespace-nowrap">{{ $customer->document ?? '-' }}</td>
                        <td class="whitespace-nowrap">{{ $customer->phone ?? '-' }}</td>
                        <td>{{ $customer->email ?? '-' }}</td>
                        <td class="mc-col-actions">
                            <div class="mc-table-actions">
                                <button
                                    type="button"
                                    wire:click="edit({{ $customer->id }})"
                                    wire:loading.attr="disabled"
                                    class="mc-btn-icon"
                                    title="Editar"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button
                                    type="button"
                                    wire:click="delete({{ $customer->id }})"
                                    wire:confirm="Excluir o cliente &quot;{{ $customer->name }}&quot;?"
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
                    <tr><td colspan="5" class="py-12 text-center text-brand-muted">Nenhum cliente cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $customers->links() }}
</div>
