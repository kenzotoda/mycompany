<div class="space-y-4">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Central de relatorios</h2>

    <div class="mc-filter-bar">
        <x-field label="Data inicial">
            <input type="date" wire:model.live="startDate" class="mc-input">
        </x-field>
        <x-field label="Data final">
            <input type="date" wire:model.live="endDate" class="mc-input">
        </x-field>
        <x-field label="Tipo">
            <x-searchable-select wire:model.live="reportType">
                <option value="all">Compras e vendas</option>
                <option value="purchases">Somente compras</option>
                <option value="sales">Somente vendas</option>
            </x-searchable-select>
        </x-field>
        <x-field label="Status">
            <x-searchable-select wire:model.live="paymentStatus">
                <option value="all">Todos status</option>
                <option value="pending">Pendente</option>
                <option value="paid">Pago</option>
                <option value="partial">Parcial</option>
            </x-searchable-select>
        </x-field>
        <div class="flex w-full flex-col">
            <span class="mc-label invisible select-none" aria-hidden="true">&nbsp;</span>
            <div class="mt-1.5 flex gap-2">
                <button type="button" wire:click="exportPdf" class="mc-btn h-10 flex-1 bg-rose-600 text-white hover:bg-rose-500 focus:ring-rose-500">Exportar PDF</button>
                <button type="button" wire:click="exportExcel" class="mc-btn h-10 flex-1 bg-emerald-600 text-white hover:bg-emerald-500 focus:ring-emerald-500">Exportar Excel</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl bg-white p-5 shadow dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Compras no periodo</p>
            <p class="text-2xl font-semibold">R$ {{ number_format($purchaseTotal, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Vendas no periodo</p>
            <p class="text-2xl font-semibold">R$ {{ number_format($salesTotal, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Lucro estimado</p>
            <p class="text-2xl font-semibold text-emerald-600">R$ {{ number_format($estimatedProfit, 2, ',', '.') }}</p>
        </div>
    </div>

    <div class="mc-table-wrap">
        <table class="mc-table">
            <thead>
                <tr>
                    <th class="w-28">Tipo</th>
                    <th class="w-28">Numero</th>
                    <th class="w-28">Data</th>
                    <th>Status</th>
                    <th class="mc-col-right w-32">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td class="whitespace-nowrap">{{ $row['type'] }}</td>
                        <td class="whitespace-nowrap">{{ $row['number'] }}</td>
                        <td class="whitespace-nowrap">{{ $row['date'] }}</td>
                        <td>{{ $row['status'] }}</td>
                        <td class="mc-col-right font-semibold">R$ {{ number_format($row['total'], 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-12 text-center text-brand-muted">Sem dados para o filtro selecionado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
