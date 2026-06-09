<div class="space-y-4">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Central de relatorios</h2>

    <div class="grid grid-cols-1 gap-3 rounded-xl bg-white p-4 shadow dark:bg-gray-800 md:grid-cols-5">
        <input type="date" wire:model.live="startDate" class="rounded-lg border-gray-300 text-sm dark:bg-gray-900">
        <input type="date" wire:model.live="endDate" class="rounded-lg border-gray-300 text-sm dark:bg-gray-900">
        <select wire:model.live="reportType" class="rounded-lg border-gray-300 text-sm dark:bg-gray-900">
            <option value="all">Compras e vendas</option>
            <option value="purchases">Somente compras</option>
            <option value="sales">Somente vendas</option>
        </select>
        <select wire:model.live="paymentStatus" class="rounded-lg border-gray-300 text-sm dark:bg-gray-900">
            <option value="all">Todos status</option>
            <option value="pending">Pendente</option>
            <option value="paid">Pago</option>
            <option value="partial">Parcial</option>
        </select>
        <div class="flex gap-2">
            <button wire:click="exportPdf" class="w-full rounded-lg bg-rose-600 px-3 py-2 text-sm font-medium text-white hover:bg-rose-500">Exportar PDF</button>
            <button wire:click="exportExcel" class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-500">Exportar Excel</button>
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

    <div class="overflow-hidden rounded-xl bg-white shadow dark:bg-gray-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-left">Numero</th>
                    <th class="px-4 py-3 text-left">Data</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($rows as $row)
                    <tr>
                        <td class="px-4 py-3">{{ $row['type'] }}</td>
                        <td class="px-4 py-3">{{ $row['number'] }}</td>
                        <td class="px-4 py-3">{{ $row['date'] }}</td>
                        <td class="px-4 py-3">{{ $row['status'] }}</td>
                        <td class="px-4 py-3 text-right">R$ {{ number_format($row['total'], 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Sem dados para o filtro selecionado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
