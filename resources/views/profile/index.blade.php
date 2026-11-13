<x-app-layout title="User Profile - GudangIn">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-foreground leading-tight">
                    {{ __('Profil Pengguna & Hak Akses') }}
                </h2>
                <p class="text-sm text-muted-foreground mt-0.5">
                    Informasi akun, jabatan, dan kategori permission yang Anda miliki di sistem GudangIn.
                </p>
            </div>
            <x-secondary-button href="{{ route('dashboard') }}" wire:navigate>
                &larr; {{ __('Kembali ke Dashboard') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- User Role & Privilege Badge Card -->
            <div class="bg-card text-card-foreground rounded-xl border border-border shadow-sm p-6 overflow-hidden relative">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 pb-6 border-b border-border">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-primary/10 border-2 border-primary/20 flex items-center justify-center text-2xl font-bold text-primary">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-xl font-bold text-foreground">{{ Auth::user()->name }}</h3>
                                @if(Auth::user()->isSuperAdmin())
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                        <span class="w-2 h-2 rounded-full bg-red-600"></span>
                                        Super Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                        Basic User
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-muted-foreground mt-0.5">
                                {{ Auth::user()->email }} &bull; Username: <span class="font-mono text-foreground font-medium">@ {{ Auth::user()->username }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="bg-muted/50 rounded-lg p-3 border border-border/60 text-right">
                        <div class="text-xs uppercase tracking-wider text-muted-foreground font-semibold">Jabatan / Posisi</div>
                        <div class="text-base font-bold text-foreground mt-0.5">{{ Auth::user()->jabatan ?? 'Warehouse Staff' }}</div>
                    </div>
                </div>

                <!-- Active Permissions Checklist -->
                <div class="mt-6">
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-muted-foreground mb-4">
                        Kategori Permission & Hak Akses
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Permission 1: Stock In -->
                        <div class="flex items-start gap-3 p-3.5 rounded-lg border border-green-200 bg-green-50/50">
                            <div class="p-1 rounded bg-green-100 text-green-700 mt-0.5">
                                <x-heroicon-o-check class="w-4 h-4" />
                            </div>
                            <div>
                                <h5 class="text-sm font-semibold text-green-900">Input Stock In (Inbound)</h5>
                                <p class="text-xs text-green-700 mt-0.5">Menerima barang dari Supplier B2B & update stok rak gudang.</p>
                            </div>
                        </div>

                        <!-- Permission 2: Stock Out -->
                        <div class="flex items-start gap-3 p-3.5 rounded-lg border border-green-200 bg-green-50/50">
                            <div class="p-1 rounded bg-green-100 text-green-700 mt-0.5">
                                <x-heroicon-o-check class="w-4 h-4" />
                            </div>
                            <div>
                                <h5 class="text-sm font-semibold text-green-900">Input Stock Out (Outbound)</h5>
                                <p class="text-xs text-green-700 mt-0.5">Pengeluaran barang ke Klien B2B dengan validasi sisa stok.</p>
                            </div>
                        </div>

                        <!-- Permission 3: Inventory Monitoring -->
                        <div class="flex items-start gap-3 p-3.5 rounded-lg border border-green-200 bg-green-50/50">
                            <div class="p-1 rounded bg-green-100 text-green-700 mt-0.5">
                                <x-heroicon-o-check class="w-4 h-4" />
                            </div>
                            <div>
                                <h5 class="text-sm font-semibold text-green-900">Dashboard & Low Stock Alert</h5>
                                <p class="text-xs text-green-700 mt-0.5">Melihat analitik barang masuk/keluar dan peringatan stok menipis.</p>
                            </div>
                        </div>

                        <!-- Permission 4: Audit & Export Excel -->
                        <div class="flex items-start gap-3 p-3.5 rounded-lg border border-green-200 bg-green-50/50">
                            <div class="p-1 rounded bg-green-100 text-green-700 mt-0.5">
                                <x-heroicon-o-check class="w-4 h-4" />
                            </div>
                            <div>
                                <h5 class="text-sm font-semibold text-green-900">Export Laporan ke Excel (.xlsx)</h5>
                                <p class="text-xs text-green-700 mt-0.5">Mengunduh data transaksi mutasi stok untuk audit dan stok opname.</p>
                            </div>
                        </div>

                        <!-- Permission 5: User Management -->
                        @if(Auth::user()->isSuperAdmin())
                            <div class="flex items-start gap-3 p-3.5 rounded-lg border border-red-200 bg-red-50/50">
                                <div class="p-1 rounded bg-red-100 text-red-700 mt-0.5">
                                    <x-heroicon-o-shield-check class="w-4 h-4" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <h5 class="text-sm font-semibold text-red-900">User Management</h5>
                                        <span class="text-[10px] font-bold uppercase px-1.5 py-0.2 bg-red-200 text-red-800 rounded">Aktif</span>
                                    </div>
                                    <p class="text-xs text-red-700 mt-0.5">Tambah user, atur jabatan, dan eskalasi privilege Super Admin.</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start gap-3 p-3.5 rounded-lg border border-gray-200 bg-gray-50/60 opacity-60">
                                <div class="p-1 rounded bg-gray-200 text-gray-500 mt-0.5">
                                    <x-heroicon-o-lock-closed class="w-4 h-4" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <h5 class="text-sm font-semibold text-gray-700">User Management</h5>
                                        <span class="text-[10px] font-medium uppercase px-1.5 py-0.2 bg-gray-200 text-gray-600 rounded">Terkunci</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">Hanya dapat diakses oleh akun kategori Super Admin.</p>
                                </div>
                            </div>
                        @endif

                        <!-- Permission 6: System Configuration -->
                        @if(Auth::user()->isSuperAdmin())
                            <div class="flex items-start gap-3 p-3.5 rounded-lg border border-red-200 bg-red-50/50">
                                <div class="p-1 rounded bg-red-100 text-red-700 mt-0.5">
                                    <x-heroicon-o-cog-6-tooth class="w-4 h-4" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <h5 class="text-sm font-semibold text-red-900">Konfigurasi Sistem Gudang</h5>
                                        <span class="text-[10px] font-bold uppercase px-1.5 py-0.2 bg-red-200 text-red-800 rounded">Aktif</span>
                                    </div>
                                    <p class="text-xs text-red-700 mt-0.5">Akses penuh pengaturan gudang, master unit, dan kategori.</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start gap-3 p-3.5 rounded-lg border border-gray-200 bg-gray-50/60 opacity-60">
                                <div class="p-1 rounded bg-gray-200 text-gray-500 mt-0.5">
                                    <x-heroicon-o-lock-closed class="w-4 h-4" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <h5 class="text-sm font-semibold text-gray-700">Konfigurasi Sistem Gudang</h5>
                                        <span class="text-[10px] font-medium uppercase px-1.5 py-0.2 bg-gray-200 text-gray-600 rounded">Terkunci</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">Hanya dapat diakses oleh akun kategori Super Admin.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Edit Personal Info Form -->
            <livewire:profile.edit-profile />

            <!-- Update Password -->
            <livewire:profile.update-password />
        </div>
    </div>
</x-app-layout>
