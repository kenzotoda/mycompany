<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 16px; margin-bottom: 8px; }
        .meta { margin-bottom: 12px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        .totals { margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Relatorio Financeiro</h1>
    <div class="meta">
        Periodo: {{ $startDate }} ate {{ $endDate }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Numero</th>
                <th>Data</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['type'] }}</td>
                    <td>{{ $row['number'] }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['status'] }}</td>
                    <td>R$ {{ number_format((float) $row['total'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Sem dados para o periodo selecionado.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <p>Compras: R$ {{ number_format((float) $purchaseTotal, 2, ',', '.') }}</p>
        <p>Vendas: R$ {{ number_format((float) $salesTotal, 2, ',', '.') }}</p>
        <p>Lucro estimado: R$ {{ number_format((float) $estimatedProfit, 2, ',', '.') }}</p>
    </div>
</body>
</html>
