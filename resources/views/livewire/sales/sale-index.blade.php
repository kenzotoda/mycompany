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
                    <th class="w-28">Número</th>
                    <th class="w-28">Data</th>
                    <th>Cliente</th>
                    <th>Descrição</th>
                    <th>Detalhes</th>
                    <th>Pagamento</th>
                    <th>Documentos</th>
                    <th class="mc-col-right w-32">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    @php
                        $description = trim((string) ($sale->notes ?? ''));
                        $details = $sale->items->map(function ($item) {
                            $productName = $item->product?->name ?? 'Produto removido';

                            return $productName
                                .' - Qtd: '.(int) $item->quantity
                                .', Unit: R$ '.number_format((float) $item->unit_price, 2, ',', '.')
                                .', Total: R$ '.number_format((float) $item->total, 2, ',', '.');
                        })->implode("\n");
                    @endphp
                    <tr>
                        <td class="whitespace-nowrap font-medium">{{ $sale->number }}</td>
                        <td class="whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                        <td>{{ $sale->customer->name ?? '-' }}</td>
                        <td class="align-top">
                            @if ($description === '')
                                <span class="mc-hint">Sem descrição</span>
                            @else
                                <button
                                    type="button"
                                    class="mc-link text-xs"
                                    data-bs-toggle="modal"
                                    data-bs-target="#sale-description-modal-{{ $sale->id }}"
                                >
                                    Ver descrição
                                </button>
                            @endif
                        </td>
                        <td class="align-top">
                            @if ($details === '')
                                <span class="mc-hint">Sem detalhes</span>
                            @else
                                <button
                                    type="button"
                                    class="mc-link text-xs"
                                    data-bs-toggle="modal"
                                    data-bs-target="#sale-details-modal-{{ $sale->id }}"
                                >
                                    Ver detalhes
                                </button>
                            @endif
                        </td>
                        <td class="whitespace-nowrap">
                            {{ \App\Support\PaymentMethods::label($sale->payment_method) }}
                            @if ($sale->is_installment && $sale->installments)
                                <span class="mc-hint">({{ $sale->installments }}x)</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex flex-wrap items-center gap-1">
                                @forelse ($sale->attachments as $attachment)
                                    <a href="{{ route('attachments.preview', $attachment) }}" target="_blank" class="mc-badge bg-brand-orange-light text-brand-orange-dark hover:bg-brand-orange hover:text-white">
                                        <i class="fa-solid fa-file-lines mr-1"></i> Ver
                                    </a>
                                @empty
                                    <span class="mc-hint">Sem anexos</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="mc-col-right font-semibold">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-12 text-center text-brand-muted">Nenhuma venda encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @foreach ($sales as $sale)
        @php
            $description = trim((string) ($sale->notes ?? ''));
            $details = $sale->items->map(function ($item) {
                $productName = $item->product?->name ?? 'Produto removido';

                return $productName
                    .' - Qtd: '.(int) $item->quantity
                    .', Unit: R$ '.number_format((float) $item->unit_price, 2, ',', '.')
                    .', Total: R$ '.number_format((float) $item->total, 2, ',', '.');
            })->implode("\n");
        @endphp

        @if ($description !== '')
            <div class="modal fade" id="sale-description-modal-{{ $sale->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Venda {{ $sale->number }} - descrição</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <p class="whitespace-pre-line text-sm text-zinc-700">{{ $description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($details !== '')
            <div class="modal fade" id="sale-details-modal-{{ $sale->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Venda {{ $sale->number }} - detalhes</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <p class="whitespace-pre-line text-sm text-zinc-700">{{ $details }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    {{ $sales->links() }}
</div>
