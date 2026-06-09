<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use Livewire\Component;

class ExecutiveDashboard extends Component
{
    public array $metrics = [];

    public function mount(DashboardService $dashboardService): void
    {
        $companyId = (int) auth()->user()?->company_id;
        $this->metrics = $companyId ? $dashboardService->metrics($companyId) : [];
    }

    public function render()
    {
        return view('livewire.dashboard.executive-dashboard');
    }
}
