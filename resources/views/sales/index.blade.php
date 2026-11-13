<x-app-layout title="Riwayat Stock Out">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-foreground leading-tight">
                    {{ __('Riwayat Stock Out (Barang Keluar)') }}
                </h2>
                <p class="text-xs text-muted-foreground mt-0.5">Daftar pengeluaran barang gudang ke customer dan mitra.</p>
            </div>
            <x-primary-button
                x-data
                x-on:click="window.location.href = '{{ route('stock-out.create') }}'"
            >
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                {{ __('+ Input Stock Out') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:sales.sales-table />
        </div>
    </div>

    <livewire:components.delete-modal />
</x-app-layout>
