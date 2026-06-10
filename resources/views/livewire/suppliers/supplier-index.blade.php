<div class="space-y-6">
    <div class="mc-page-header">
        <div>
            <h1 class="mc-page-title"><i class="fa-solid fa-truck-field mc-icon"></i> Fornecedores</h1>
            <p class="mc-page-subtitle">Cadastre e gerencie seus fornecedores</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mc-alert-success"><i class="fa-solid fa-circle-check mr-2"></i>{{ session('status') }}</div>
    @endif

    <form id="supplier-form" class="mc-card mc-form-section" wire:key="supplier-form-{{ $editingId ?? 'create' }}" data-validate-supplier-document novalidate x-on:keydown.enter.prevent="window.submitValidatedForm($el, () => $wire.save(), $wire)">
        <h3 class="mc-card-title">
            <i class="fa-solid {{ $editingId ? 'fa-pen-to-square' : 'fa-plus' }} mr-2 text-brand-orange"></i>
            {{ $editingId ? 'Editar fornecedor' : 'Cadastrar fornecedor' }}
        </h3>

        <div class="mc-form-grid">
            <x-field label="Nome do fornecedor" required>
                <input type="text" wire:model.live="name" class="mc-input" placeholder="Razão social ou nome" required data-error-required="Informe o nome do fornecedor.">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
            <x-field label="CNPJ" required class="md:col-span-2" wire:key="supplier-document-cnpj-{{ $editingId ?? 'new' }}">
                <x-masked-input mask="cnpj" store-digits wire:model.live="document" maxlength="18" placeholder="00.000.000/0000-00" required data-validate-document data-validate-cnpj />
                @error('document') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
            <x-field label="Telefone">
                <x-masked-input mask="phone" wire:model.live="phone" maxlength="15" placeholder="(11) 99999-9999" data-validate-phone-if-filled />
                @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
            <x-field label="E-mail">
                <input type="email" wire:model.live="email" class="mc-input" placeholder="contato@fornecedor.com">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button
                type="button"
                wire:loading.attr="disabled"
                x-on:click="window.submitValidatedForm($el.closest('form'), () => $wire.save(), $wire)"
                class="mc-btn-primary"
            >
                <i class="fa-solid fa-floppy-disk"></i>
                {{ $editingId ? 'Salvar alterações' : 'Salvar fornecedor' }}
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
                    <th class="w-40">CNPJ</th>
                    <th class="w-36">Telefone</th>
                    <th>E-mail</th>
                    <th class="mc-col-actions">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr class="{{ $editingId === $supplier->id ? 'bg-orange-50/60' : '' }}">
                        <td class="font-medium">{{ $supplier->name }}</td>
                        <td class="whitespace-nowrap">{{ \App\Support\BrazilianDocument::formatCnpj($supplier->document) }}</td>
                        <td class="whitespace-nowrap">{{ $supplier->phone ?? '-' }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>
                        <td class="mc-col-actions">
                            <div class="mc-table-actions">
                                <button
                                    type="button"
                                    wire:click="edit({{ $supplier->id }})"
                                    wire:loading.attr="disabled"
                                    class="mc-btn-icon"
                                    title="Editar"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button
                                    type="button"
                                    wire:click="delete({{ $supplier->id }})"
                                    wire:confirm="Excluir o fornecedor &quot;{{ $supplier->name }}&quot;?"
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
                    <tr><td colspan="5" class="py-12 text-center text-brand-muted">Nenhum fornecedor cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $suppliers->links() }}
</div>
