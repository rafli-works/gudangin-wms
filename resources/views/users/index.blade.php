<x-app-layout title="User Management - GudangIn">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-xl text-foreground leading-tight">
                        {{ __('User Management & Privilege') }}
                    </h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 border border-red-300">
                        Super Admin Only
                    </span>
                </div>
                <p class="text-sm text-muted-foreground mt-1">
                    Kelola data staf gudang, penambahan user, perizinan, dan tingkatan privilege pengguna.
                </p>
            </div>
            <x-primary-button x-data x-on:click="$dispatch('create-user')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                {{ __('Tambah User Baru') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-card text-card-foreground rounded-xl border border-border shadow-sm p-4">
                <livewire:users.user-table />
            </div>
        </div>
    </div>

    <livewire:users.user-form />
    <livewire:users.user-detail />
</x-app-layout>
