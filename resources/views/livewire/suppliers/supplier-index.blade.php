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

    <form wire:submit="save" class="mc-card mc-form-section">
        <h3 class="mc-card-title"><i class="fa-solid fa-plus mr-2 text-brand-orange"></i>Cadastrar fornecedor</h3>

        <div class="mc-form-grid">
            <x-field label="Nome do fornecedor" required>
                <input type="text" wire:model="name" class="mc-input" placeholder="Razão social ou nome">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
            <x-field label="CNPJ" required>
                <x-masked-input mask="cnpj" wire:model="document" maxlength="18" placeholder="00.000.000/0000-00" />
                @error('document') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
            <x-field label="Telefone">
                <x-masked-input mask="phone" wire:model="phone" maxlength="15" placeholder="(11) 99999-9999" />
                @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
            <x-field label="E-mail">
                <input type="email" wire:model="email" class="mc-input" placeholder="contato@fornecedor.com">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </x-field>
        </div>

        <button type="submit" class="mc-btn-primary">
            <i class="fa-solid fa-floppy-disk"></i> Salvar fornecedor
        </button>
    </form>

    <div class="mc-table-wrap">
        <table class="mc-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CNPJ</th>
                    <th>Telefone</th>
                    <th>E-mail</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td class="font-medium">{{ $supplier->name }}</td>
                        <td>{{ $supplier->document }}</td>
                        <td>{{ $supplier->phone ?? '-' }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-12 text-center text-brand-muted">Nenhum fornecedor cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $suppliers->links() }}
</div>
