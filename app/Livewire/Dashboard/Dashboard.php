<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Livewire\Component;
use App\Enums\DatePeriod;
use App\Services\DashboardStatsService;

class Dashboard extends Component
{
    public string $dateFilter = DatePeriod::TODAY->value;
    public ?string $customStartDate = null;
    public ?string $customEndDate = null;

    public array $warehouseMetrics = [];
    public array $lowStockProducts = [];
    public array $topProducts = [];
    public array $topSuppliers = [];

    // Charts Data
    public array $outboundChart = [];
    public array $stockMovementChart = [];

    public function mount(DashboardStatsService $service)
    {
        $this->loadStats($service);
    }

    public function updatedDateFilter()
    {
        if ($this->dateFilter !== DatePeriod::CUSTOM->value) {
            $this->loadStats(app(DashboardStatsService::class));
        }
    }

    public function updateCustomRange($startDate, $endDate)
    {
        $this->customStartDate = $startDate;
        $this->customEndDate = $endDate;

        if ($this->dateFilter === DatePeriod::CUSTOM->value) {
            $this->loadStats(app(DashboardStatsService::class));
        }
    }

    public function loadStats(DashboardStatsService $service)
    {
        [$startDate, $endDate] = $this->getDateRange();

        // 1. Warehouse Key Metrics
        $this->warehouseMetrics = $service->getWarehouseMetrics();

        // 2. Low Stock Products
        $this->lowStockProducts = $service->getLowStockProducts(5);

        // 3. Top Outbound Products (Produk Terbanyak Keluar)
        $this->topProducts = $service->getTopProducts($startDate, $endDate, 5);

        // 4. Top Suppliers (Pemasok dengan barang terbanyak dikirim)
        $this->topSuppliers = $service->getTopSuppliers($startDate, $endDate, 5);

        // 5. Outbound Items Trend (Banyak barang keluar dalam interval waktu)
        $outboundTrend = $service->getOutboundTrend($startDate, $endDate);
        $this->outboundChart = [
            'labels' => array_keys($outboundTrend),
            'data' => array_values($outboundTrend),
        ];

        // 6. Stock In vs Stock Out Movement (Grafik barang masuk vs barang keluar harian)
        $this->stockMovementChart = $service->getStockMovementTrend($startDate, $endDate);

        $this->dispatch('stats-updated', [
            'outbound' => $this->outboundChart,
            'stockMovement' => $this->stockMovementChart,
        ]);
    }

    protected function getDateRange(): array
    {
        $now = Carbon::now();

        return match(DatePeriod::tryFrom($this->dateFilter)) {
            DatePeriod::TODAY => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            DatePeriod::YESTERDAY => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            DatePeriod::THIS_WEEK => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            DatePeriod::THIS_MONTH => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            DatePeriod::LAST_MONTH => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            DatePeriod::CUSTOM => [
                Carbon::parse($this->customStartDate)->startOfDay(),
                Carbon::parse($this->customEndDate)->endOfDay()
            ],
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
        };
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard');
    }
}
