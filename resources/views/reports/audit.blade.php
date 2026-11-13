<x-app-layout title="Laporan Audit Gudang & Export Excel - GudangIn">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-xl text-foreground leading-tight">
                        {{ __('Laporan Audit Stok & Mutasi Pergudangan') }}
                    </h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        Audit Ready
                    </span>
                </div>
                <p class="text-sm text-muted-foreground mt-1">
                    Rekapitulasi sisa stok fisik, mutasi inbound & outbound, serta perhitungan nilai aset gudang untuk kebutuhan audit & stok opname.
                </p>
            </div>
            
            <a href="{{ route('reports.audit.export', request()->query()) }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors bg-emerald-600 hover:bg-emerald-700 text-white h-10 px-4 py-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                {{ __('Export ke Excel (.xlsx)') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Card -->
            <div class="bg-card text-card-foreground rounded-xl border border-border shadow-sm p-5">
                <form method="GET" action="{{ route('reports.audit') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-xs font-medium text-muted-foreground mb-1">Cari Nama Barang / SKU</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Contoh: SKU-001 / Semen" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-medium text-muted-foreground mb-1">Kategori Barang</label>
                        <select name="category_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="block text-xs font-medium text-muted-foreground mb-1">Mulai Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- End Date & Filter Action -->
                    <div>
                        <label class="block text-xs font-medium text-muted-foreground mb-1">Sampai Tanggal</label>
                        <div class="flex gap-2">
                            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-card text-card-foreground rounded-xl border border-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-border flex justify-between items-center bg-muted/20">
                    <span class="text-sm font-semibold">Tabel Audit Mutasi Fisik Barang</span>
                    <span class="text-xs text-muted-foreground">Menampilkan {{ $products->total() }} barang terdaftar</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-border bg-muted/40 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                <th class="p-3">SKU & Nama Barang</th>
                                <th class="p-3">Kategori</th>
                                <th class="p-3 text-right">Inbound (Masuk)</th>
                                <th class="p-3 text-right">Outbound (Keluar)</th>
                                <th class="p-3 text-right">Sisa Stok Fisik</th>
                                <th class="p-3 text-right">Nilai Aset</th>
                                <th class="p-3 text-center">Status Audit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($products as $p)
                                @php
                                    $inbound = (int) ($p->total_inbound ?? 0);
                                    $outbound = (int) ($p->total_outbound ?? 0);
                                    $stock = (int) $p->quantity;
                                    $minStock = (int) $p->min_stock;
                                    $assetValue = $stock * (float) $p->purchase_price;
                                @endphp
                                <tr class="hover:bg-muted/30 transition-colors">
                                    <td class="p-3">
                                        <div class="font-semibold text-foreground">{{ $p->name }}</div>
                                        <div class="text-xs font-mono text-muted-foreground">{{ $p->sku }}</div>
                                    </td>
                                    <td class="p-3 text-muted-foreground">
                                        {{ $p->category->name ?? '-' }}
                                    </td>
                                    <td class="p-3 text-right text-emerald-600 font-medium">
                                        +{{ number_format($inbound) }} {{ $p->unit->name ?? 'pcs' }}
                                    </td>
                                    <td class="p-3 text-right text-red-600 font-medium">
                                        -{{ number_format($outbound) }} {{ $p->unit->name ?? 'pcs' }}
                                    </td>
                                    <td class="p-3 text-right font-bold text-foreground">
                                        {{ number_format($stock) }} {{ $p->unit->name ?? 'pcs' }}
                                    </td>
                                    <td class="p-3 text-right font-mono text-xs font-medium text-foreground">
                                        Rp {{ number_format($assetValue, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-center">
                                        @if($stock <= 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800">
                                                HABIS
                                            </span>
                                        @elseif($stock <= $minStock)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800">
                                                MENIPIS
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800">
                                                AMAN
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-muted-foreground">
                                        Tidak ada data barang yang sesuai dengan filter pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($products->hasPages())
                    <div class="p-4 border-t border-border">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

