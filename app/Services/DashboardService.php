<?php

namespace App\Services;

use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;

class DashboardService
{
    public function metrics(int $companyId): array
    {
        $monthStart = now()->startOfMonth();

        $sales = Sale::where('company_id', $companyId)->whereDate('sale_date', '>=', $monthStart);
        $purchases = Purchase::where('company_id', $companyId)->whereDate('purchase_date', '>=', $monthStart);

        $revenue = (float) $sales->sum('total');
        $cost = (float) $purchases->sum('total');

        return [
            'faturamento_mes' => $revenue,
            'lucro_estimado' => $revenue - $cost,
            'total_compras' => (float) $purchases->count(),
            'total_vendas' => (float) $sales->count(),
            'contas_receber' => (float) AccountReceivable::where('company_id', $companyId)->where('status', 'pending')->sum('amount'),
            'contas_pagar' => (float) AccountPayable::where('company_id', $companyId)->where('status', 'pending')->sum('amount'),
            'baixo_estoque' => Product::where('company_id', $companyId)->where('stock_quantity', '<', 1)->count(),
        ];
    }
}
