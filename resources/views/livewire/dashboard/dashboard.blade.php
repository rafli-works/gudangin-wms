<div>
    <div class="space-y-6">
        <!-- Filter Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-card p-4 rounded-xl border border-border shadow-sm">
            <div>
                <h2 class="text-lg font-bold text-foreground">Dashboard Operasional Gudang</h2>
                <p class="text-xs text-muted-foreground mt-0.5">Pantau analitik arus barang masuk (Inbound), barang keluar (Outbound), dan status stok.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Period Selector -->
                <select wire:model.live="dateFilter" class="h-9 w-[180px] rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                    @foreach(\App\Enums\DatePeriod::cases() as $period)
                        <option value="{{ $period->value }}">{{ $period->label() }}</option>
                    @endforeach
                </select>

                <!-- Custom Date Range (Flatpickr) -->
                <div x-show="$wire.dateFilter === 'custom'" x-transition class="flex items-center gap-2"
                     x-data="{
                         init() {
                             flatpickr(this.$refs.picker, {
                                 mode: 'range',
                                 dateFormat: 'Y-m-d',
                                 defaultDate: [this.$wire.customStartDate, this.$wire.customEndDate],
                                 onChange: (selectedDates, dateStr, instance) => {
                                     if (selectedDates.length === 2) {
                                         this.$wire.updateCustomRange(
                                             instance.formatDate(selectedDates[0], 'Y-m-d'),
                                             instance.formatDate(selectedDates[1], 'Y-m-d')
                                         );
                                     }
                                 }
                             });
                         }
                     }"
                >
                    <input x-ref="picker" type="text" class="h-9 w-[240px] rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" placeholder="Pilih rentang tanggal...">
                </div>

                <!-- Refresh Button -->
                <button wire:click="$refresh" class="print:hidden inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-9" title="Segarkan Data">
                    <x-heroicon-o-arrow-path wire:loading.class="animate-spin" class="h-4 w-4" />
                </button>
                
                <!-- Print Button -->
                <button onclick="window.print()" class="print:hidden inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 gap-2">
                    <x-heroicon-o-printer class="h-4 w-4" />
                    <span class="hidden sm:inline">Cetak Laporan</span>
                </button>
            </div>
        </div>

        <!-- Low Stock Alert Banner (Jika Ada Stok Menipis) -->
        @if(count($lowStockProducts) > 0)
            <div class="rounded-xl border border-amber-300 bg-amber-50/80 p-4 text-amber-900 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-200/80 text-amber-900 rounded-lg">
                        <x-heroicon-o-exclamation-triangle class="h-5 w-5" />
                    </div>
                    <div>
                        <h4 class="text-sm font-bold">Peringatan: {{ count($lowStockProducts) }} Barang Berstatus Low Stock (Menipis)</h4>
                        <p class="text-xs text-amber-800 mt-0.5">Kuantitas stok barang ini sudah berada di bawah atau sama dengan batas minimum stok gudang.</p>
                    </div>
                </div>
                <a href="{{ route('low-stock.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-600 hover:bg-amber-700 text-white shadow-xs whitespace-nowrap">
                    Lihat Barang Menipis &rarr;
                </a>
            </div>
        @endif

        <!-- GudangIn Warehouse Key Metrics Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Total SKU -->
            <div class="rounded-xl border border-indigo-100 bg-gradient-to-br from-indigo-50/50 to-background p-4 shadow-sm">
                <div class="flex items-center justify-between pb-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-700">Total SKU Barang</h3>
                    <div class="p-2 bg-indigo-100 text-indigo-700 rounded-lg">
                        <x-heroicon-o-cube class="h-4 w-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-foreground">
                    {{ number_format($warehouseMetrics['total_sku'] ?? 0) }}
                </div>
                <p class="text-xs text-muted-foreground mt-1">Item aktif terdaftar di sistem gudang</p>
            </div>

            <!-- Total Physical Stock -->
            <div class="rounded-xl border border-sky-100 bg-gradient-to-br from-sky-50/50 to-background p-4 shadow-sm">
                <div class="flex items-center justify-between pb-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-sky-700">Total Stok Fisik</h3>
                    <div class="p-2 bg-sky-100 text-sky-700 rounded-lg">
                        <x-heroicon-o-archive-box class="h-4 w-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-foreground">
                    {{ number_format($warehouseMetrics['total_stock'] ?? 0) }}
                </div>
                <p class="text-xs text-muted-foreground mt-1">Total unit barang di rak gudang saat ini</p>
            </div>

            <!-- Monthly Inbound -->
            <div class="rounded-xl border border-emerald-100 bg-gradient-to-br from-emerald-50/50 to-background p-4 shadow-sm">
                <div class="flex items-center justify-between pb-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-700">Barang Masuk (Bulan Ini)</h3>
                    <div class="p-2 bg-emerald-100 text-emerald-700 rounded-lg">
                        <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-600">
                    +{{ number_format($warehouseMetrics['inbound_monthly'] ?? 0) }}
                </div>
                <p class="text-xs text-muted-foreground mt-1">Unit diterima dari Supplier B2B</p>
            </div>

            <!-- Monthly Outbound -->
            <div class="rounded-xl border border-rose-100 bg-gradient-to-br from-rose-50/50 to-background p-4 shadow-sm">
                <div class="flex items-center justify-between pb-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-rose-700">Barang Keluar (Bulan Ini)</h3>
                    <div class="p-2 bg-rose-100 text-rose-700 rounded-lg">
                        <x-heroicon-o-arrow-up-tray class="h-4 w-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-rose-600">
                    -{{ number_format($warehouseMetrics['outbound_monthly'] ?? 0) }}
                </div>
                <p class="text-xs text-muted-foreground mt-1">Unit terdistribusi ke Klien B2B</p>
            </div>
        </div>

        <!-- Charts Section (Outbound Trend & Stock Movement) -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-2">
            <!-- Outbound Items Trend -->
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 break-inside-avoid">
                <div class="flex flex-col space-y-1 pb-2">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-base text-foreground">Tren Barang Keluar (Outbound Trend)</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800">
                            Volume Keluar
                        </span>
                    </div>
                    <p class="text-xs text-muted-foreground">Banyak unit barang keluar berdasarkan time interval yang dipilih.</p>
                </div>
                <div class="pt-2" wire:ignore>
                    <div id="outboundChart" class="w-full h-[280px]"></div>
                </div>
            </div>

            <!-- Stock In vs Stock Out Movement -->
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4 break-inside-avoid">
                <div class="flex flex-col space-y-1 pb-2">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-base text-foreground">Stock In vs Stock Out</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                            Arus Mutasi Harian
                        </span>
                    </div>
                    <p class="text-xs text-muted-foreground">Grafik perbandingan kuantitas barang masuk (Inbound) vs barang keluar (Outbound).</p>
                </div>
                <div class="pt-2" wire:ignore>
                    <div id="stockMovementChart" class="w-full h-[280px]"></div>
                </div>
            </div>
        </div>

        <!-- Bottom Ranking Grid (Top Outbound Products & Top Suppliers) -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-2">
            <!-- Top Outbound Products -->
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm break-inside-avoid">
                <div class="p-4 flex flex-col space-y-1 border-b border-border">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-base text-foreground">Top Produk Terbanyak Keluar</h3>
                        <x-heroicon-o-fire class="w-4 h-4 text-amber-500" />
                    </div>
                    <p class="text-xs text-muted-foreground">Daftar produk dengan akumulasi kuantitas pengeluaran tertinggi.</p>
                </div>
                <div class="p-4 max-h-[350px] overflow-auto">
                    <div class="space-y-3">
                        @forelse($topProducts as $product)
                            <div class="flex items-center justify-between p-2.5 rounded-lg border border-border/60 hover:bg-muted/40 transition-colors">
                                <div class="space-y-0.5 flex-1 pr-3">
                                    <p class="text-sm font-bold text-foreground truncate" title="{{ $product['product_name'] }}">{{ $product['product_name'] }}</p>
                                    <p class="text-xs font-mono text-muted-foreground">{{ $product['sku'] }}</p>
                                </div>
                                <div class="font-bold text-sm bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-md whitespace-nowrap">
                                    {{ number_format($product['total_sold']) }} <span class="text-xs font-normal">Unit Keluar</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-muted-foreground text-center py-6">Belum ada riwayat pengeluaran barang pada periode ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Top Suppliers -->
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm break-inside-avoid">
                <div class="p-4 flex flex-col space-y-1 border-b border-border">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-base text-foreground">Top Supplier (Pemasok Teraktif)</h3>
                        <x-heroicon-o-truck class="w-4 h-4 text-emerald-600" />
                    </div>
                    <p class="text-xs text-muted-foreground">Pemasok B2B dengan volume barang kiriman terbanyak.</p>
                </div>
                <div class="p-4 max-h-[350px] overflow-auto">
                    <div class="space-y-3">
                        @forelse($topSuppliers as $supplier)
                            <div class="flex items-center justify-between p-2.5 rounded-lg border border-border/60 hover:bg-muted/40 transition-colors">
                                <div class="space-y-0.5 flex-1 pr-3">
                                    <p class="text-sm font-bold text-foreground truncate" title="{{ $supplier['supplier_name'] }}">{{ $supplier['supplier_name'] }}</p>
                                    <p class="text-xs text-muted-foreground flex items-center gap-1">
                                        <x-heroicon-o-phone class="w-3 h-3" /> {{ $supplier['phone'] ?? 'Tidak ada kontak' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-sm bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-md whitespace-nowrap">
                                        +{{ number_format($supplier['total_supplied_qty']) }} <span class="text-xs font-normal">Unit Diterima</span>
                                    </div>
                                    <div class="text-[10px] text-muted-foreground mt-0.5">
                                        {{ $supplier['total_shipments'] }} Pengiriman
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-muted-foreground text-center py-6">Belum ada transaksi penerimaan barang dari supplier pada periode ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    @media print {
        @page { size: landscape; margin: 1cm; }
        body { background-color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .print\:hidden { display: none !important; }
        .bg-card { border: 1px solid #e2e8f0; box-shadow: none !important; }
        .grid { gap: 1rem !important; }
        .break-inside-avoid { break-inside: avoid; page-break-inside: avoid; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        let outboundChart = null;
        let stockMovementChart = null;

        const initCharts = (data) => {
            // 1. Outbound Items Trend Chart (Area Chart)
            const outboundOptions = {
                series: [{
                    name: 'Barang Keluar (Outbound)',
                    data: data.outbound.data
                }],
                chart: {
                    type: 'area',
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    parentHeightOffset: 0
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2.5 },
                xaxis: {
                    categories: data.outbound.labels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { cssClass: 'text-[11px] text-muted-foreground' }
                    }
                },
                yaxis: {
                    labels: {
                        style: { cssClass: 'text-[11px] text-muted-foreground' },
                        formatter: (val) => Math.round(val) + ' Unit'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return new Intl.NumberFormat('id-ID').format(val) + ' Unit Keluar';
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.65,
                        opacityTo: 0.15,
                        stops: [0, 90, 100]
                    }
                },
                colors: ['#2563eb'], // Blue 600
            };

            // 2. Stock In vs Stock Out Movement Chart (Double Bar Chart)
            const stockMovementOptions = {
                series: [{
                    name: 'Barang Masuk (Stock In)',
                    data: data.stockMovement.inbound
                }, {
                    name: 'Barang Keluar (Stock Out)',
                    data: data.stockMovement.outbound
                }],
                chart: {
                    type: 'bar',
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    parentHeightOffset: 0
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 4
                    },
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: {
                    categories: data.stockMovement.labels,
                    labels: {
                        style: { cssClass: 'text-[11px] text-muted-foreground' }
                    }
                },
                yaxis: {
                    labels: {
                        style: { cssClass: 'text-[11px] text-muted-foreground' },
                        formatter: (val) => Math.round(val) + ' Unit'
                    }
                },
                colors: ['#10b981', '#ef4444'], // Emerald (Inbound), Red (Outbound)
                fill: { opacity: 1 },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return new Intl.NumberFormat('id-ID').format(val) + ' Unit';
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px'
                }
            };

            if (outboundChart) outboundChart.destroy();
            if (stockMovementChart) stockMovementChart.destroy();

            outboundChart = new ApexCharts(document.querySelector("#outboundChart"), outboundOptions);
            outboundChart.render();

            stockMovementChart = new ApexCharts(document.querySelector("#stockMovementChart"), stockMovementOptions);
            stockMovementChart.render();
        };

        // Initial Load
        initCharts({
            outbound: @json($outboundChart),
            stockMovement: @json($stockMovementChart)
        });

        // Listen for server-side updates on interval change
        Livewire.on('stats-updated', (data) => {
            initCharts(data[0]);
        });
    });
</script>
</div>
