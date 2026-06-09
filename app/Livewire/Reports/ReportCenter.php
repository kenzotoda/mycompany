<?php

namespace App\Livewire\Reports;

use App\Exports\FinancialReportExport;
use App\Models\Purchase;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ReportCenter extends Component
{
    public string $startDate = '';
    public string $endDate = '';
    public string $reportType = 'all';
    public string $paymentStatus = 'all';

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function render()
    {
        $data = $this->buildReport();

        return view('livewire.reports.report-center', $data);
    }

    public function exportPdf()
    {
        $data = $this->buildReport();
        $pdf = Pdf::loadView('reports.summary-pdf', [
            ...$data,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'relatorio-financeiro-'.now()->format('Ymd-His').'.pdf'
        );
    }

    public function exportExcel()
    {
        $data = $this->buildReport();
        $rows = collect($data['rows'])->map(fn ($row) => [
            $row['type'],
            $row['number'],
            $row['date'],
            $row['status'],
            $row['total'],
        ])->values()->all();

        return Excel::download(
            new FinancialReportExport($rows),
            'relatorio-financeiro-'.now()->format('Ymd-His').'.xlsx'
        );
    }

    private function buildReport(): array
    {
        $companyId = auth()->user()->company_id;

        $purchaseQuery = Purchase::where('company_id', $companyId)
            ->whereBetween('purchase_date', [$this->startDate, $this->endDate]);

        $saleQuery = Sale::where('company_id', $companyId)
            ->whereBetween('sale_date', [$this->startDate, $this->endDate]);

        if ($this->paymentStatus !== 'all') {
            $purchaseQuery->where('payment_status', $this->paymentStatus);
            $saleQuery->where('payment_status', $this->paymentStatus);
        }

        $purchases = $this->reportType === 'sales' ? collect() : $purchaseQuery->get(['number', 'purchase_date', 'payment_status', 'total']);
        $sales = $this->reportType === 'purchases' ? collect() : $saleQuery->get(['number', 'sale_date', 'payment_status', 'total']);

        $rows = collect()
            ->merge($purchases->map(fn ($row) => [
                'type' => 'Compra',
                'number' => $row->number,
                'date' => $row->purchase_date->format('d/m/Y'),
                'sort_date' => $row->purchase_date->toDateString(),
                'status' => $row->payment_status,
                'total' => (float) $row->total,
            ]))
            ->merge($sales->map(fn ($row) => [
                'type' => 'Venda',
                'number' => $row->number,
                'date' => $row->sale_date->format('d/m/Y'),
                'sort_date' => $row->sale_date->toDateString(),
                'status' => $row->payment_status,
                'total' => (float) $row->total,
            ]))
            ->sortByDesc('sort_date')
            ->map(function ($row) {
                unset($row['sort_date']);
                return $row;
            })
            ->values();

        $purchaseTotal = (float) $purchases->sum('total');
        $salesTotal = (float) $sales->sum('total');

        return [
            'rows' => $rows,
            'purchaseTotal' => $purchaseTotal,
            'salesTotal' => $salesTotal,
            'estimatedProfit' => $salesTotal - $purchaseTotal,
        ];
    }
}
