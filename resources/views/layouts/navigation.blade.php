<section class="py-4 bg-background border-b border-border shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ mobileMenuOpen: false }">
        <!-- Desktop Menu -->
        <nav class="hidden items-center justify-between lg:flex">
            <div class="flex items-center gap-6">
                <!-- Logo & Brand -->
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <img src="{{ asset('favicon.png') }}" alt="GudangIn" class="w-10 h-10 rounded-xl shadow-xs object-contain hover:opacity-90 transition-opacity">
                </a>

                <!-- Navigation Menu -->
                <div class="flex items-center">
                    <div class="flex flex-row gap-1">
                        <!-- Dashboard Link -->
                        <a href="{{ route('dashboard') }}" class="group inline-flex h-10 w-max items-center justify-center rounded-md px-3.5 py-2 text-sm font-medium transition-colors hover:bg-muted hover:text-accent-foreground {{ request()->routeIs('dashboard') ? 'bg-accent/50 text-accent-foreground font-semibold' : 'bg-background' }}">
                            <x-heroicon-o-squares-2x2 class="mr-1.5 h-4 w-4" />
                            Dashboard
                        </a>

                        <!-- Stock In Dropdown -->
                        <x-nav-dropdown active="{{ request()->routeIs(['stock-in.*', 'purchases.*', 'suppliers.*']) }}">
                            <x-slot name="icon">
                                <x-heroicon-o-arrow-down-tray class="mr-1.5 h-4 w-4 text-emerald-600" />
                            </x-slot>
                            <x-slot name="trigger">
                                Stock In
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('stock-in.create')" :active="request()->routeIs(['stock-in.*', 'purchases.*'])">
                                    + Input Barang Masuk
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                                    Supplier
                                </x-dropdown-link>
                            </x-slot>
                        </x-nav-dropdown>

                        <!-- Stock Out Dropdown -->
                        <x-nav-dropdown active="{{ request()->routeIs(['stock-out.*', 'sales.*', 'customers.*']) }}">
                            <x-slot name="icon">
                                <x-heroicon-o-arrow-up-tray class="mr-1.5 h-4 w-4 text-blue-600" />
                            </x-slot>
                            <x-slot name="trigger">
                                Stock Out
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('stock-out.create')" :active="request()->routeIs(['stock-out.*', 'sales.*'])">
                                    + Input Barang Keluar
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                                    Customer
                                </x-dropdown-link>
                            </x-slot>
                        </x-nav-dropdown>

                        <!-- Products / Inventory Dropdown -->
                        <x-nav-dropdown active="{{ request()->routeIs(['products.*', 'categories.*', 'units.*', 'low-stock.*']) }}">
                            <x-slot name="icon">
                                <x-heroicon-o-cube class="mr-1.5 h-4 w-4" />
                            </x-slot>
                            <x-slot name="trigger">
                                Inventaris
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                                    Katalog Barang (SKU)
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('low-stock.index')" :active="request()->routeIs('low-stock.*')">
                                    Low Stock Alert
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                                    Kategori Barang
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('units.index')" :active="request()->routeIs('units.*')">
                                    Satuan Unit
                                </x-dropdown-link>
                            </x-slot>
                        </x-nav-dropdown>

                        <!-- Audit & Reports Link -->
                        <a href="{{ route('reports.audit') }}" class="group inline-flex h-10 w-max items-center justify-center rounded-md px-3.5 py-2 text-sm font-medium transition-colors hover:bg-muted hover:text-accent-foreground {{ request()->routeIs('reports.*') ? 'bg-accent/50 text-accent-foreground font-semibold' : 'bg-background' }}">
                            <x-heroicon-o-document-chart-bar class="mr-1.5 h-4 w-4 text-emerald-600" />
                            Audit
                        </a>

                        <!-- User Management Link (Super Admin Exclusive) -->
                        @if(Auth::user()->isSuperAdmin())
                            <a href="{{ route('users.index') }}" class="group inline-flex h-10 w-max items-center justify-center rounded-md px-3.5 py-2 text-sm font-medium transition-colors hover:bg-muted hover:text-accent-foreground {{ request()->routeIs('users.*') ? 'bg-accent/50 text-accent-foreground font-semibold' : 'bg-background' }}">
                                <x-heroicon-o-shield-check class="mr-1.5 h-4 w-4 text-red-600" />
                                User Management
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- User Auth Buttons -->
            <div class="flex items-center gap-3">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-lg border border-border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted focus-visible:outline-none gap-2">
                            <div class="flex flex-col text-right">
                                <span class="text-xs font-bold leading-tight">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] text-muted-foreground">{{ Auth::user()->jabatan ?? 'Warehouse Staff' }}</span>
                            </div>
                            @if(Auth::user()->isSuperAdmin())
                                <span class="w-2.5 h-2.5 rounded-full bg-red-600 ring-2 ring-red-100" title="Super Admin"></span>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 ring-2 ring-blue-100" title="Basic User"></span>
                            @endif
                            <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-muted-foreground" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-border">
                            <p class="text-xs text-muted-foreground">Login sebagai:</p>
                            <p class="text-sm font-bold text-foreground">{{ Auth::user()->name }}</p>
                            <div class="mt-1">
                                @if(Auth::user()->isSuperAdmin())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">
                                        Super Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                        Basic User
                                    </span>
                                @endif
                            </div>
                        </div>

                        <x-dropdown-link :href="route('profile.index')" :active="request()->routeIs('profile.*')">
                            <x-heroicon-o-user class="w-4 h-4 mr-2 inline" />
                            {{ __('Profil & Hak Akses') }}
                        </x-dropdown-link>

                        @if(Auth::user()->isSuperAdmin())
                            <x-dropdown-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                                <x-heroicon-o-cog-6-tooth class="w-4 h-4 mr-2 inline" />
                                {{ __('Pengaturan Sistem') }}
                            </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="text-red-600 hover:text-red-700">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 mr-2 inline" />
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div class="block lg:hidden">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <img src="{{ asset('favicon.png') }}" alt="GudangIn" class="w-9 h-9 rounded-xl shadow-xs object-contain">
                </a>

                <button @click="mobileMenuOpen = true" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background hover:bg-accent h-10 w-10">
                    <x-heroicon-o-bars-3 class="h-4 w-4" />
                </button>
            </div>

            <!-- Mobile Sheet/Drawer -->
            <div x-show="mobileMenuOpen"
                x-transition:enter="duration-300 ease-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="duration-200 ease-in"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm"
                style="display: none;"
                @click="mobileMenuOpen = false">
            </div>

            <div x-show="mobileMenuOpen"
                x-transition:enter="duration-500 ease-in-out"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="duration-500 ease-in-out"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed inset-y-0 right-0 z-50 h-full w-3/4 gap-4 border-l bg-background p-6 shadow-lg sm:max-w-sm overflow-y-auto"
                style="display: none;"
                @click.stop>

                <div class="flex flex-col gap-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('favicon.png') }}" alt="GudangIn" class="w-8 h-8 rounded-lg shadow-sm object-contain">
                            <span class="text-lg font-bold">GudangIn WMS</span>
                        </div>
                        <button @click="mobileMenuOpen = false" class="rounded-sm opacity-70 hover:opacity-100">
                            <x-heroicon-o-x-mark class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="flex w-full flex-col gap-4 text-sm font-medium">
                        <a href="{{ route('dashboard') }}" class="py-1 hover:text-primary {{ request()->routeIs('dashboard') ? 'text-primary font-bold' : '' }}">Dashboard</a>

                        <div class="font-bold text-xs uppercase text-muted-foreground mt-2">Logistik Pergudangan</div>
                        <a href="{{ route('stock-in.create') }}" class="pl-2 hover:text-primary {{ request()->routeIs(['stock-in.*', 'purchases.*']) ? 'text-primary font-bold' : '' }}">Stock In</a>
                        <a href="{{ route('suppliers.index') }}" class="pl-2 hover:text-primary {{ request()->routeIs('suppliers.*') ? 'text-primary font-bold' : '' }}">Supplier</a>

                        <a href="{{ route('stock-out.create') }}" class="pl-2 hover:text-primary {{ request()->routeIs(['stock-out.*', 'sales.*']) ? 'text-primary font-bold' : '' }}">Stock Out</a>
                        <a href="{{ route('customers.index') }}" class="pl-2 hover:text-primary {{ request()->routeIs('customers.*') ? 'text-primary font-bold' : '' }}">Customer</a>

                        <div class="font-bold text-xs uppercase text-muted-foreground mt-2">Inventaris & Audit</div>
                        <a href="{{ route('products.index') }}" class="pl-2 hover:text-primary">Katalog Barang (SKU)</a>
                        <a href="{{ route('low-stock.index') }}" class="pl-2 hover:text-primary">Low Stock Alert</a>
                        <a href="{{ route('reports.audit') }}" class="pl-2 hover:text-emerald-600 font-semibold text-emerald-600">Audit</a>

                        @if(Auth::user()->isSuperAdmin())
                            <div class="font-bold text-xs uppercase text-muted-foreground mt-2">Super Admin</div>
                            <a href="{{ route('users.index') }}" class="pl-2 hover:text-primary text-red-600 font-semibold">User Management</a>
                            <a href="{{ route('settings.index') }}" class="pl-2 hover:text-primary">Pengaturan Sistem</a>
                        @endif

                        <div class="pt-4 mt-4 border-t border-border">
                            <div class="font-bold text-sm">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-muted-foreground mb-3">{{ Auth::user()->role_label }} &bull; {{ Auth::user()->jabatan }}</div>
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('profile.index') }}" class="inline-flex items-center justify-center rounded-md text-xs font-medium border border-input h-8 px-3">
                                    Profil & Hak Akses
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center rounded-md text-xs font-semibold bg-red-600 text-white h-8 px-3">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
