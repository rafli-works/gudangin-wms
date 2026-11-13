<div class="space-y-6">
    <!-- Header Input Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 p-5 rounded-xl border border-gray-200">
        <!-- Supplier -->
        <div class="space-y-2">
            <x-input-label for="supplier_id" :value="__('Supplier')" required />
            <div class="w-full">
                <select id="supplier_id" name="supplier_id"
                        x-init="initSupplierSelect($el)"
                        x-model="supplier_id"
                        autocomplete="off">
                    <option value=""></option>
                    @if(old('supplier_id'))
                        @php
                            $oldSupplier = \App\Models\Supplier::find(old('supplier_id'));
                        @endphp
                        @if($oldSupplier)
                            <option value="{{ $oldSupplier->id }}" selected>{{ $oldSupplier->name . ($oldSupplier->phone ? ' | ' . $oldSupplier->phone : '') }}</option>
                        @endif
                    @elseif(isset($purchase) && $purchase->supplier)
                        <option value="{{ $purchase->supplier_id }}" selected>{{ $purchase->supplier->name . ($purchase->supplier->phone ? ' | ' . $purchase->supplier->phone : '') }}</option>
                    @endif
                </select>
            </div>
            <x-input-error :messages="$errors->get('supplier_id')" />
        </div>

        <!-- In Date -->
        <div class="space-y-2">
            <x-input-label for="purchase_date" :value="__('In Date')" required />
            <x-text-input
                id="purchase_date"
                type="date"
                name="purchase_date"
                :value="old('purchase_date', $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : date('Y-m-d'))"
                class="block w-full text-sm"
            />
            <x-input-error :messages="$errors->get('purchase_date')" />
        </div>

        <!-- Total Quantity Counter (Pengganti Due Date) -->
        <div class="space-y-2">
            <x-input-label :value="__('Total Kuantitas Masuk')" />
            <div class="flex h-10 w-full items-center justify-between rounded-md border border-gray-300 bg-white px-3 text-sm font-bold text-indigo-700 shadow-xs">
                <span>Total Barang:</span>
                <span x-text="totalQuantity + ' Unit'">0 Unit</span>
            </div>
            <!-- Hidden Due Date for backend validation compatibility -->
            <input type="hidden" name="due_date" :value="document.getElementById('purchase_date')?.value || '{{ date('Y-m-d') }}'">
        </div>

        <!-- Notes -->
        <div class="md:col-span-3 space-y-2">
            <x-input-label for="notes" :value="__('Keterangan / Lokasi Rak')" />
            <textarea
                id="notes"
                name="notes"
                rows="2"
                class="flex min-h-[70px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                placeholder="Catatan tambahan nomor surat jalan, lokasi rak, atau kondisi barang..."
            >{{ old('notes', $purchase->notes ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('notes')" />
        </div>
    </div>

    <!-- Items Section -->
    <div class="space-y-4">
        <!-- Search Bar -->
        <div class="relative z-20">
             <select
                id="master_product_search"
                x-init="initMasterSearch($el)"
                placeholder="Cari Barang / SKU untuk ditambahkan ke daftar Stock In..."
                autocomplete="off"
            ></select>
        </div>

        <!-- Cart Table (Only Product, Qty, Supplier, Action) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-36">Qty</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Supplier</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(item, index) in items" :key="item.key">
                            <tr :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'" class="hover:bg-indigo-50/50 transition-colors">
                                <!-- Product Name -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900" x-text="item.product_name"></div>
                                    <div class="text-xs font-mono text-gray-500" x-text="item.product_code || 'SKU: ' + item.product_id"></div>
                                    <input type="hidden" :name="`items[${index}][product_name]`" :value="item.product_name">
                                    <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                                    <input type="hidden" :name="`items[${index}][product_code]`" :value="item.product_code">
                                    <input type="hidden" :name="`items[${index}][unit_price]`" :value="item.unit_price || 0">
                                    <input type="hidden" :name="`items[${index}][selling_price]`" :value="item.selling_price || 0">
                                </td>

                                <!-- Qty -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <input
                                            type="number"
                                            :name="`items[${index}][quantity]`"
                                            x-model.number="item.quantity"
                                            class="w-24 text-center border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold shadow-xs py-1.5"
                                            min="1"
                                            placeholder="1"
                                        >
                                    </div>
                                    <p x-show="hasError(`items.${index}.quantity`)" x-text="getError(`items.${index}.quantity`)" class="text-xs text-red-600 mt-1"></p>
                                </td>

                                <!-- Supplier -->
                                <td class="px-6 py-4 whitespace-nowrap text-left text-sm text-gray-600">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200" x-text="selectedSupplierName">
                                    </span>
                                </td>

                                <!-- Action -->
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button @click="removeItem(index)" type="button" class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 focus:outline-none transition-colors mx-auto" title="Hapus Barang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <template x-if="items.length === 0">
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        <p class="text-sm font-bold text-gray-700">No items added</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Search products above to add to purchase list</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr>
                            <td class="px-6 py-3.5 text-left font-bold text-gray-700 text-sm">Total Barang Masuk:</td>
                            <td class="px-6 py-3.5 text-center font-black text-indigo-700 text-base">
                                <span x-text="totalQuantity + ' Unit'">0 Unit</span>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-x-3 pt-6 border-t border-gray-200">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background hover:bg-accent h-10 px-4 py-2">
            {{ __('Cancel') }}
        </a>

        <x-primary-button class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700" ::disabled="loading || items.length === 0">
            <svg x-show="loading" class="animate-spin -ml-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-text="loading ? 'Processing...' : 'Save'">Save</span>
        </x-primary-button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('purchaseForm', (initialData) => ({
            items: (initialData.items || []).map(i => ({
                ...i,
                key: i.key || Math.random().toString(36).substr(2, 9),
                quantity: parseInt(i.quantity) || 1,
                unit_price: parseFloat(i.unit_price) || 0,
                selling_price: parseFloat(i.selling_price) || 0,
                subtotal: (parseFloat(i.unit_price) || 0) * (parseInt(i.quantity) || 1)
            })),
            supplier_id: initialData.supplier_id || '',
            selectedSupplierName: 'Pilih Supplier Terlebih Dahulu',
            status: initialData.status || 'draft',
            loading: false,
            errors: initialData.errors || {},

            init() {},

            hasError(field) {
                return !!this.errors[field];
            },

            getError(field) {
                return this.errors[field] ? this.errors[field][0] : '';
            },

            submitForm(e) {
                if (this.loading) return;
                this.loading = true;
                e.target.submit();
            },

            removeItem(index) {
                this.items.splice(index, 1);
            },

            get totalQuantity() {
                return this.items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
            },

            get total() {
                return this.items.reduce((sum, item) => sum + ((parseFloat(item.unit_price) || 0) * (parseInt(item.quantity) || 0)), 0);
            },

            waitForTomSelect(callback) {
                if (window.TomSelect) {
                    callback();
                } else {
                    setTimeout(() => this.waitForTomSelect(callback), 50);
                }
            },

            initSupplierSelect(el) {
                let self = this;
                this.waitForTomSelect(() => {
                    new TomSelect(el, {
                        placeholder: 'Pilih Supplier...',
                        preload: 'focus',
                        valueField: 'value',
                        labelField: 'text',
                        searchField: 'text',
                        onChange: function(value) {
                            self.supplier_id = value;
                            let option = this.options[value];
                            self.selectedSupplierName = option ? option.text.split('|')[0].trim() : 'Supplier';
                        },
                        load: function(query, callback) {
                            var url = '{{ route("ajax.suppliers.search") }}';

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
                            }).catch(() => {
                                callback();
                            });
                        }
                    });
                });
            },

            addProduct(product) {
                let existingIndex = this.items.findIndex(item => item.product_id === product.value);

                if (existingIndex > -1) {
                    this.items[existingIndex].quantity += 1;
                } else {
                    this.items.push({
                        key: Math.random().toString(36).substr(2, 9),
                        product_id: product.value,
                        product_name: product.text,
                        product_code: product.sku || '',
                        quantity: 1,
                        unit_price: product.price || 0,
                        selling_price: product.selling_price || 0,
                        subtotal: (product.price || 0) * 1
                    });

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: 'Barang "' + product.text + '" ditambahkan.',
                            type: 'success'
                        }
                    }));
                }
            },

            initMasterSearch(el) {
                let self = this;
                this.waitForTomSelect(() => {
                    let ts = new TomSelect(el, {
                        placeholder: 'Cari Barang / SKU untuk ditambahkan...',
                        preload: 'focus',
                        valueField: 'value',
                        labelField: 'text',
                        searchField: 'text',
                        closeAfterSelect: false,
                        openOnFocus: true,
                        load: function(query, callback) {
                            var url = '{{ route("ajax.products.search") }}';

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
                            }).catch(() => {
                                callback();
                            });
                        },
                        onItemAdd: function(value, item) {
                            let data = this.options[value];
                            if (data) {
                                self.addProduct(data);
                            }
                            this.clear(true);
                            this.focus();
                        }
                    });
                });
            }
        }));
    });
</script>
@endpush
