<x-app-layout title="Stock In">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-foreground leading-tight">
                    {{ __('Stock In') }}
                </h2>
                <p class="text-xs text-muted-foreground mt-0.5">Formulir penerimaan dan penambahan stok barang masuk ke gudang.</p>
            </div>
            <x-secondary-button href="{{ route('dashboard') }}">
                &larr; {{ __('Dashboard') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="p-2 bg-emerald-100 rounded-lg text-emerald-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </span>
                        <div>
                            <p class="font-bold text-sm">Berhasil!</p>
                            <p class="text-xs text-emerald-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl flex items-center gap-3 shadow-xs">
                    <span class="p-2 bg-red-100 rounded-lg text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </span>
                    <div>
                        <p class="font-bold text-sm">Gagal Menyimpan</p>
                        <p class="text-xs text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('stock-in.store') }}" method="POST" enctype="multipart/form-data"
                    x-data="purchaseForm({
                        items: {{ Js::from(old('items', [])) }},
                        supplier_id: {{ Js::from(old('supplier_id')) }},
                        status: {{ Js::from(old('status', 'draft')) }},
                        errors: {{ Js::from($errors->any() ? $errors->toArray() : []) }}
                    })"
                    @submit.prevent="submitForm">
                @csrf

                @include('purchases.form')

            </form>
        </div>
    </div>
</x-app-layout>
