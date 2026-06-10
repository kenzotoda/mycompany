<div class="space-y-6">
    <div class="mc-page-header">
        <div>
            <h1 class="mc-page-title"><i class="fa-solid fa-cart-shopping mc-icon"></i> Compras</h1>
            <p class="mc-page-subtitle">Histórico de compras registradas</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="mc-btn-primary">
            <i class="fa-solid fa-plus"></i> Nova compra
        </a>
    </div>

    @if (session('status'))
        <div class="mc-alert-success"><i class="fa-solid fa-circle-check mr-2"></i>{{ session('status') }}</div>
    @endif

    <div class="mc-table-wrap">
        <table class="mc-table">
            <thead>
                <tr>
                    <th class="w-28">Número</th>
                    <th class="w-28">Data</th>
                    <th>Fornecedor</th>
                    <th>Pagamento</th>
                    <th>Documentos</th>
                    <th class="mc-col-right w-32">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchases as $purchase)
                    <tr>
                        <td class="whitespace-nowrap font-medium">{{ $purchase->number }}</td>
                        <td class="whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
                        <td>{{ $purchase->supplier->name ?? '-' }}</td>
                        <td class="whitespace-nowrap">{{ \App\Support\PaymentMethods::label($purchase->payment_method) }}</td>
                        <td>
                            <div class="flex flex-wrap items-center gap-1">
                                @forelse ($purchase->attachments as $attachment)
                                    <a href="{{ route('attachments.preview', $attachment) }}" target="_blank" class="mc-badge bg-brand-orange-light text-brand-orange-dark hover:bg-brand-orange hover:text-white">
                                        <i class="fa-solid fa-file-lines mr-1"></i> Ver
                                    </a>
                                @empty
                                    <span class="mc-hint">Sem anexos</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="mc-col-right font-semibold">R$ {{ number_format($purchase->total, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-12 text-center text-brand-muted">Nenhuma compra encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $purchases->links() }}
</div>
