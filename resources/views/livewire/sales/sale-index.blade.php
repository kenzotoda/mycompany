<div class="space-y-6">
    <div class="mc-page-header">
        <div>
            <h1 class="mc-page-title"><i class="fa-solid fa-cash-register mc-icon"></i> Vendas</h1>
            <p class="mc-page-subtitle">Histórico de vendas realizadas</p>
        </div>
        <a href="{{ route('sales.create') }}" class="mc-btn-primary">
            <i class="fa-solid fa-plus"></i> Nova venda
        </a>
    </div>

    <div class="mc-table-wrap">
        <table class="mc-table">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Pagamento</th>
                    <th>Documentos</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td class="font-medium">{{ $sale->number }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                        <td>{{ $sale->customer->name ?? '-' }}</td>
                        <td>
                            {{ \App\Support\PaymentMethods::label($sale->payment_method) }}
                            @if ($sale->is_installment && $sale->installments)
                                <span class="mc-hint">({{ $sale->installments }}x)</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @forelse ($sale->attachments as $attachment)
                                    <a href="{{ route('attachments.preview', $attachment) }}" target="_blank" class="mc-badge bg-brand-orange-light text-brand-orange-dark hover:bg-brand-orange hover:text-white">
                                        <i class="fa-solid fa-file-lines mr-1"></i> Ver
                                    </a>
                                @empty
                                    <span class="mc-hint">Sem anexos</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="text-right font-semibold">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-12 text-center text-brand-muted">Nenhuma venda encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $sales->links() }}
</div>
