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
                    <th>Descrição</th>
                    <th>Detalhes</th>
                    <th>Pagamento</th>
                    <th>Documentos</th>
                    <th class="mc-col-right w-32">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchases as $purchase)
                    @php
                        $description = trim((string) ($purchase->notes ?? ''));
                        $details = $purchase->items->map(function ($item) {
                            $productName = $item->product?->name ?? 'Produto removido';

                            return $productName
                                .' - Qtd: '.(int) $item->quantity
                                .', Unit: R$ '.number_format((float) $item->unit_price, 2, ',', '.')
                                .', Total: R$ '.number_format((float) $item->total, 2, ',', '.');
                        })->implode("\n");
                    @endphp
                    <tr>
                        <td class="whitespace-nowrap font-medium">{{ $purchase->number }}</td>
                        <td class="whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
                        <td>{{ $purchase->supplier->name ?? '-' }}</td>
                        <td class="align-top">
                            @if ($description === '')
                                <span class="mc-hint">Sem descrição</span>
                            @else
                                <button
                                    type="button"
                                    class="mc-link text-xs"
                                    data-bs-toggle="modal"
                                    data-bs-target="#purchase-description-modal-{{ $purchase->id }}"
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
                                    data-bs-target="#purchase-details-modal-{{ $purchase->id }}"
                                >
                                    Ver detalhes
                                </button>
                            @endif
                        </td>
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
                    <tr><td colspan="8" class="py-12 text-center text-brand-muted">Nenhuma compra encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @foreach ($purchases as $purchase)
        @php
            $description = trim((string) ($purchase->notes ?? ''));
            $details = $purchase->items->map(function ($item) {
                $productName = $item->product?->name ?? 'Produto removido';

                return $productName
                    .' - Qtd: '.(int) $item->quantity
                    .', Unit: R$ '.number_format((float) $item->unit_price, 2, ',', '.')
                    .', Total: R$ '.number_format((float) $item->total, 2, ',', '.');
            })->implode("\n");
        @endphp

        @if ($description !== '')
            <div class="modal fade" id="purchase-description-modal-{{ $purchase->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Compra {{ $purchase->number }} - descrição</h5>
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
            <div class="modal fade" id="purchase-details-modal-{{ $purchase->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Compra {{ $purchase->number }} - detalhes</h5>
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

    {{ $purchases->links() }}
</div>
