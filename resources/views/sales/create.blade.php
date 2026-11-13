<x-app-layout title="Stock Out">
    <div class="mx-auto sm:px-6 lg:px-8 py-4"
         x-data="pos()"
         x-init="init()"
         @keydown.window.f1.prevent="productTs && productTs.focus()"
         @keydown.window.f2.prevent="customerTs && customerTs.focus()"
         @keydown.window.f3.prevent="openConfirmation()"
         @keydown.window.f4.prevent="openCustomerModal()"
    >
        <!-- Header -->
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex p-2 bg-rose-50 text-rose-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </span>
                    Stock Out
                </h1>
                <p class="text-sm text-gray-500">Pencatatan pengeluaran barang dari gudang ke Customer</p>
            </div>
            <div class="text-sm text-gray-500">
                <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Dashboard</a>
                <span class="mx-1">/</span>
                <span class="text-gray-700 font-semibold">Stock Out</span>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row h-[calc(100vh-160px)] space-y-4 lg:space-y-0 lg:space-x-4 relative">

            <!-- Left Side: Transaction Details (70%) -->
            <div class="w-full lg:w-[70%] flex flex-col space-y-4 h-full">
                <!-- Search Bar (TomSelect) -->
                <div class="relative z-20 mb-2">
                    <select
                        x-ref="productSelect"
                        placeholder="Search Products (Name or SKU) [F1]..."
                        autocomplete="off"
                    ></select>
                </div>

                <!-- Cart Table -->
                <div class="flex-1 bg-white rounded-lg shadow border border-gray-200 overflow-hidden flex flex-col">
                    <div class="overflow-x-auto flex-1">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="(item, index) in cart" :key="item.id">
                                    <tr :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'" class="hover:bg-indigo-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900" x-text="item.name"></div>
                                            <div class="text-xs text-gray-500" x-text="item.sku"></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center">
                                                <input type="number" x-model="item.quantity" min="1" :max="item.max_stock"
                                                    @input="validateQty(index)"
                                                    class="w-24 text-center border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm shadow-sm font-semibold"
                                                    placeholder="1">
                                            </div>
                                            <div x-show="item.quantity > item.max_stock" class="text-xs text-red-600 mt-1">
                                                Max: <span x-text="item.max_stock"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-600" x-text="item.unit || 'Pcs'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900">
                                            <span x-text="item.quantity"></span> <span class="text-xs font-normal text-gray-500" x-text="item.unit || 'Pcs'"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <button @click="removeFromCart(index)" class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 focus:outline-none transition-colors mx-auto">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="cart.length === 0">
                                    <tr>
                                        <td colspan="5" class="px-6 py-20 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                <p class="text-base font-medium">Cart is empty</p>
                                                <p class="text-sm text-gray-400">Search products above to start transaction</p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side: Stock Out Details (30%) -->
            <div class="w-full lg:w-[30%] flex flex-col bg-white rounded-lg shadow border border-gray-200 h-full">
                <!-- Header -->
                <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Detail Pengeluaran (Stock Out)</h2>
                </div>

                <div class="p-4 space-y-6 flex-1 overflow-y-auto">
                    <!-- Customer Section -->
                    <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100 relative group">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-indigo-500 uppercase">Customer</span>
                            <button @click="openCustomerModal()" class="text-[10px] font-semibold text-indigo-600 hover:text-white hover:bg-indigo-600 border border-indigo-200 bg-white px-2 py-1 rounded transition-colors flex items-center">
                                + New (F4)
                            </button>
                        </div>

                        <div class="relative">
                            <template x-if="selectedCustomer">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-bold text-lg text-gray-900" x-text="selectedCustomer.name"></h3>
                                        <p class="text-sm text-gray-600" x-text="selectedCustomer.phone || 'No Phone'"></p>
                                    </div>
                                    <button @click="resetCustomer()" class="text-gray-400 hover:text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </template>
                            <div x-show="!selectedCustomer">
                                <select
                                    x-ref="customerSelect"
                                    placeholder="Search Customer [F2]..."
                                    autocomplete="off"
                                ></select>
                            </div>
                        </div>
                    </div>

                    <!-- Quantity Summary Section -->
                    <div class="space-y-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <div class="flex justify-between items-center text-gray-600 text-sm font-medium">
                            <span>Total Item (SKU)</span>
                            <span class="font-bold text-gray-800" x-text="cart.length + ' Item'"></span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600 text-sm font-medium">
                            <span>Quantity Produk Keluar</span>
                            <span class="font-bold text-gray-800" x-text="totalQty + ' Unit'"></span>
                        </div>
                        <div class="pt-4 border-t border-slate-200 flex justify-between items-baseline">
                            <span class="text-sm font-bold text-gray-700 uppercase tracking-wide">TOTAL KUANTITAS</span>
                            <div class="text-right">
                                <span class="text-3xl font-extrabold text-blue-600" x-text="totalQty"></span>
                                <span class="text-sm font-medium text-gray-500 ml-1">Unit</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Catatan Pengeluaran</label>
                        <textarea
                            x-model="payment.notes"
                            rows="3"
                            class="block w-full text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-400 py-2"
                            placeholder="Catatan / No. Surat Jalan / Tujuan distribusi..."
                        ></textarea>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-4 border-t border-gray-200 bg-gray-50 flex gap-3">
                    <button
                        @click="$dispatch('open-modal', { name: 'cancel-modal' })"
                        class="w-1/3 py-3 text-sm font-bold text-red-600 hover:text-white bg-white border border-red-200 hover:bg-red-600 rounded-lg flex items-center justify-center transition-colors shadow-sm"
                    >
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        CANCEL
                    </button>

                    <button
                        @click="openConfirmation()"
                        :disabled="isSubmitting || cart.length === 0"
                        class="w-2/3 flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <template x-if="isSubmitting">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <span x-text="isSubmitting ? 'Processing...' : 'Save'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Alpine Component Logic -->
        <script>
            function pos() {
                return {
                    cart: [],
                    selectedCustomer: null,
                    payment: {
                        method: 'cash',
                        cash_received: 0,
                        notes: ''
                    },
                    globalDiscount: 0,
                    saleStatus: 'completed',
                    isSubmitting: false,

                    // TomSelect Instances
                    productTs: null,
                    customerTs: null,
                    lastSearchQuery: '',

                    init() {
                        const savedCart = localStorage.getItem('pos_cart');
                        if (savedCart) this.cart = JSON.parse(savedCart);

                        const savedCustomer = localStorage.getItem('pos_customer');
                        if (savedCustomer) this.selectedCustomer = JSON.parse(savedCustomer);

                        const savedNotes = localStorage.getItem('pos_notes');
                        if (savedNotes) this.payment.notes = savedNotes;

                        this.$watch('cart', (val) => localStorage.setItem('pos_cart', JSON.stringify(val)));
                        this.$watch('selectedCustomer', (val) => localStorage.setItem('pos_customer', JSON.stringify(val)));
                        this.$watch('payment.notes', (val) => localStorage.setItem('pos_notes', val));

                        this.initProductSelect();
                        this.initCustomerSelect();
                    },

                    initProductSelect() {
                        if (!this.$refs.productSelect) return;

                        if (this.productTs) {
                            this.productTs.destroy();
                            this.productTs = null;
                        }

                        this.productTs = new TomSelect(this.$refs.productSelect, {
                            valueField: 'id',
                            labelField: 'name',
                            searchField: ['name', 'sku'],
                            closeAfterSelect: false,
                            openOnFocus: true,
                            preload: 'focus',
                            load: (query, callback) => {
                                this.lastSearchQuery = query;

                                fetch('{{ route("ajax.products.search") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({ q: query })
                                })
                                    .then(response => response.json())
                                    .then(json => {
                                        callback(json);
                                    }).catch(()=>{
                                        callback();
                                    });
                            },
                            render: {
                                option: (item, escape) => {
                                    return `
                                        <div class="py-2 px-3 border-b border-gray-100">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <div class="font-medium text-gray-900">${escape(item.name)}</div>
                                                    <div class="text-xs text-gray-500">${escape(item.sku)}</div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-xs font-semibold ${item.quantity > 0 ? 'text-green-600' : 'text-red-600'}">
                                                        Stok: ${escape(item.quantity)} ${escape(item.unit?.symbol || 'Pcs')}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                },
                                item: (item, escape) => {
                                    return `<div>${escape(item.name)}</div>`;
                                }
                            },
                            onChange: (value) => {
                                if (value) {
                                    const item = this.productTs.options[value];
                                    if(item) {
                                        this.addToCart(item);

                                        const cleanup = () => {
                                            this.productTs.clear(true);
                                            this.productTs.setTextboxValue(this.lastSearchQuery);
                                            this.productTs.refreshOptions(false);
                                        };
                                        setTimeout(cleanup, 10);
                                    }
                                }
                            }
                        });
                    },

                    initCustomerSelect() {
                        if (!this.$refs.customerSelect) return;

                        if (this.customerTs) {
                            this.customerTs.destroy();
                            this.customerTs = null;
                        }

                        this.customerTs = new TomSelect(this.$refs.customerSelect, {
                            valueField: 'value',
                            labelField: 'text',
                            searchField: 'text',
                            preload: 'focus',
                            openOnFocus: true,
                            load: (query, callback) => {
                                var url = '{{ route("ajax.customers.search") }}';

                                fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({ q: query })
                                })
                                    .then(response => response.json())
                                    .then(json => {
                                        callback(json);
                                    }).catch(()=>{
                                        callback();
                                    });
                            },
                            render: {
                                option: (item, escape) => {
                                    return `
                                        <div class="py-2 px-3 hover:bg-indigo-50">
                                            <div class="font-medium text-gray-900">${escape(item.name)}</div>
                                            <div class="text-xs text-gray-500">${escape(item.phone || 'No Phone')}</div>
                                        </div>
                                    `;
                                }
                            },
                            onChange: (value) => {
                                if (value) {
                                    const item = this.customerTs.options[value];
                                    if(item) {
                                        this.selectedCustomer = {
                                            id: item.value,
                                            name: item.name,
                                            phone: item.phone,
                                            email: item.email || '',
                                            address: item.address || ''
                                        };
                                        this.customerTs.clear();
                                    }
                                }
                            }
                        });
                    },

                    resetCustomer() {
                        this.selectedCustomer = null;
                        this.$nextTick(() => {
                            this.customerTs && this.customerTs.focus();
                        });
                    },

                    clearStorage() {
                        localStorage.removeItem('pos_cart');
                        localStorage.removeItem('pos_customer');
                        localStorage.removeItem('pos_notes');
                    },

                    // Cart Management
                    addToCart(product) {
                        const existing = this.cart.find(item => item.id === product.id);
                        if (existing) {
                            if (existing.quantity < product.quantity) {
                                existing.quantity++;
                                this.$dispatch('toast', { message: 'Produk sudah ada di list. Kuantitas ditambah.', type: 'info' });
                            } else {
                                this.$dispatch('toast', { message: 'Stok gudang tidak mencukupi!', type: 'error' });
                            }
                        } else {
                            if (product.quantity > 0) {
                                this.cart.push({
                                    id: product.id,
                                    name: product.name,
                                    sku: product.sku,
                                    price: product.selling_price || 0,
                                    quantity: 1,
                                    max_stock: product.quantity,
                                    unit: product.unit ? product.unit.symbol : 'Pcs',
                                    discount: 0
                                });
                                this.$dispatch('toast', { message: 'Produk "' + product.name + '" ditambahkan.', type: 'success' });
                            } else {
                                this.$dispatch('toast', { message: 'Stok habis!', type: 'error' });
                            }
                        }
                    },

                    validateQty(index) {
                        const item = this.cart[index];
                        if (item.quantity > item.max_stock) {
                            item.quantity = item.max_stock;
                            this.$dispatch('toast', { message: 'Maksimum stok tercapai (' + item.max_stock + ')', type: 'warning' });
                        }
                        if (item.quantity < 1 || !item.quantity) item.quantity = 1;
                    },

                    removeFromCart(index) {
                        const removedItem = this.cart[index];
                        this.cart.splice(index, 1);
                        this.$dispatch('toast', { message: 'Produk "' + removedItem.name + '" dihapus.', type: 'info' });
                    },

                    openCustomerModal() {
                        this.$dispatch('open-modal', { name: 'customer-modal' });
                        this.$nextTick(() => {
                            setTimeout(() => {
                                this.$refs.nameInput && this.$refs.nameInput.focus();
                            }, 100);
                        });
                    },

                    get totalQty() {
                        return this.cart.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
                    },

                    openConfirmation() {
                        if (this.cart.length === 0) {
                            this.$dispatch('toast', { message: 'Pilih minimal satu barang untuk dikeluarkan!', type: 'error' });
                            return;
                        }
                        this.$dispatch('open-modal', { name: 'confirmation-modal' });
                    },

                    async submitSale() {
                        this.isSubmitting = true;

                        try {
                            const items = this.cart.map(item => ({
                                product_id: item.id,
                                quantity: parseInt(item.quantity) || 1,
                                unit_price: item.price || 0,
                                discount: 0
                            }));

                            const payload = {
                                customer_id: this.selectedCustomer?.id,
                                items: items,
                                payment_method: 'cash',
                                cash_received: 0,
                                notes: this.payment.notes || '',
                                global_discount: 0,
                                status: 'completed',
                                sale_date: new Date().toISOString().slice(0, 10),
                                _token: '{{ csrf_token() }}'
                            };

                            const res = await fetch('{{ route("stock-out.store") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify(payload)
                            });

                            const data = await res.json();

                            if (res.ok && data.success) {
                                this.$dispatch('close-modal', { name: 'confirmation-modal' });
                                this.clearStorage();
                                this.resetForm();
                                this.$dispatch('toast', { message: 'Stock Out berhasil disimpan!', type: 'success' });
                            } else {
                                this.$dispatch('toast', { message: data.message || 'Terjadi kesalahan sistem', type: 'error' });
                            }

                        } catch (e) {
                            console.error(e);
                            this.$dispatch('toast', { message: 'Terjadi gangguan jaringan', type: 'error' });
                        } finally {
                            this.isSubmitting = false;
                        }
                    },

                    resetForm() {
                        this.cart = [];
                        this.selectedCustomer = null;
                        this.payment = {
                            method: 'cash',
                            cash_received: 0,
                            notes: ''
                        };
                        this.productTs && this.productTs.clear();
                        this.customerTs && this.customerTs.clear();
                    }
                }
            }
        </script>

        <!-- Confirmation Modal -->
        <x-modal name="confirmation-modal" focusable>
            <div class="p-6">
                <!-- Header -->
                <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-bold leading-none tracking-tight text-gray-900">
                        Konfirmasi Simpan Stock Out
                    </h3>
                    <p class="text-sm text-gray-500">
                        Periksa kembali rincian barang keluar sebelum menyimpan transaksi.
                    </p>
                </div>

                <!-- Summary Grid -->
                <div class="space-y-3 py-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 font-medium">Customer</span>
                        <span class="font-bold text-gray-900" x-text="selectedCustomer ? selectedCustomer.name : 'Umum / Retail'"></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 font-medium">Total Item (SKU)</span>
                        <span class="font-bold text-gray-900" x-text="cart.length + ' Item'"></span>
                    </div>
                    <div class="flex items-center justify-between text-sm border-t border-gray-100 pt-3">
                        <span class="text-gray-700 font-semibold">Total Kuantitas Keluar</span>
                        <span class="text-xl font-extrabold text-blue-600" x-text="totalQty + ' Unit'"></span>
                    </div>
                    <div class="text-xs text-gray-500 pt-2" x-show="payment.notes">
                        <span class="font-semibold text-gray-700">Catatan:</span>
                        <p class="mt-1 italic bg-gray-50 p-2 rounded border border-gray-200" x-text="payment.notes"></p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 border-t border-gray-200 pt-4 flex gap-3">
                    <x-secondary-button
                        type="button"
                        @click="$dispatch('close-modal', { name: 'confirmation-modal' })"
                        class="w-1/3 justify-center"
                    >
                        Batal
                    </x-secondary-button>

                    <button
                        @click="submitSale()"
                        :disabled="isSubmitting"
                        class="w-2/3 flex justify-center items-center py-2.5 px-4 rounded-lg shadow-sm text-base font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none disabled:opacity-50 transition-colors"
                    >
                        <template x-if="isSubmitting">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Save'"></span>
                    </button>
                </div>
            </div>
        </x-modal>

        <!-- Create Customer Modal -->
        <x-modal name="customer-modal" focusable>
            <div class="p-6" x-data="{
                newCust: { name: '', email: '', phone: '', address: '', notes: '' },
                errors: {},
                loading: false,
                async save() {
                    this.errors = {};

                    if (!this.newCust.name.trim()) {
                        this.errors.name = 'Nama wajib diisi.';
                        return;
                    }

                    this.loading = true;
                    try {
                        const res = await fetch('{{ route("ajax.customers.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.newCust)
                        });
                        const data = await res.json();

                        if (res.ok) {
                            this.$dispatch('close-modal', { name: 'customer-modal' });
                            this.$dispatch('customer-created', data);
                            this.newCust = { name: '', email: '', phone: '', address: '', notes: '' };
                            this.errors = {};
                        } else {
                            if (data.errors) {
                                Object.keys(data.errors).forEach(key => {
                                    this.errors[key] = data.errors[key][0];
                                });
                            } else {
                                this.$dispatch('toast', { message: data.message || 'Error creating customer', type: 'error' });
                            }
                        }
                    } catch(e) { console.error(e); }
                    finally { this.loading = false; }
                }
            }">
                <!-- Header -->
                <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                        {{ __('Tambah Customer Baru') }}
                    </h3>
                    <p class="text-sm text-muted-foreground">
                        {{ __('Masukkan detail customer untuk transaksi Stock Out.') }}
                    </p>
                </div>

                <div class="space-y-4">
                    <!-- Name -->
                    <div>
                        <x-form-input
                            name="new_name"
                            label="Nama Customer"
                            x-model="newCust.name"
                            x-ref="nameInput"
                            required
                        />
                        <p x-show="errors.name" x-text="errors.name" class="text-sm font-medium text-red-600 mt-1" style="display: none;"></p>
                    </div>

                    <!-- Contact Info -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-1/2">
                            <x-form-input
                                name="new_email"
                                label="Email"
                                type="email"
                                x-model="newCust.email"
                            />
                            <p x-show="errors.email" x-text="errors.email" class="text-sm font-medium text-red-600 mt-1" style="display: none;"></p>
                        </div>
                        <div class="w-full sm:w-1/2">
                            <x-form-input
                                name="new_phone"
                                label="Nomor Telepon"
                                x-model="newCust.phone"
                            />
                            <p x-show="errors.phone" x-text="errors.phone" class="text-sm font-medium text-red-600 mt-1" style="display: none;"></p>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="space-y-2">
                        <x-input-label for="new_address" :value="__('Alamat')" />
                        <textarea
                            id="new_address"
                            x-model="newCust.address"
                            rows="3"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Alamat lengkap customer..."
                        ></textarea>
                        <p x-show="errors.address" x-text="errors.address" class="text-sm font-medium text-red-600 mt-1" style="display: none;"></p>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-2">
                        <x-input-label for="new_notes" :value="__('Catatan Tambahan')" />
                        <textarea
                            id="new_notes"
                            x-model="newCust.notes"
                            rows="2"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Catatan tambahan jika ada..."
                        ></textarea>
                        <p x-show="errors.notes" x-text="errors.notes" class="text-sm font-medium text-red-600 mt-1" style="display: none;"></p>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-4">
                        <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'customer-modal' })">
                            {{ __('Batal') }}
                        </x-secondary-button>

                        <x-primary-button type="button" @click="save()" x-bind:disabled="loading">
                            <template x-if="loading">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Menyimpan...' : 'Simpan Customer'"></span>
                        </x-primary-button>
                    </div>
                </div>
            </div>
        </x-modal>

        <!-- Cancel Confirmation Modal -->
        <x-modal name="cancel-modal" focusable>
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Batalkan Transaksi Stock Out?
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Apakah Anda yakin ingin membatalkan transaksi ini? Semua item yang telah dipilih akan dibersihkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                <x-danger-button @click="resetForm(); clearStorage(); $dispatch('close-modal', { name: 'cancel-modal' }); $dispatch('toast', { message: 'Transaksi dibatalkan', type: 'info' })" class="w-full sm:w-auto justify-center">
                    {{ __('Ya, Batalkan') }}
                </x-danger-button>
                <button
                    type="button"
                    @click="$dispatch('close-modal', { name: 'cancel-modal' })"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors"
                >
                    {{ __('Kembali') }}
                </button>
            </div>
        </x-modal>

        <!-- Listen for customer created -->
        <div @customer-created.window="selectedCustomer = $event.detail; customerSearch = '';"></div>
    </div>
</x-app-layout>
