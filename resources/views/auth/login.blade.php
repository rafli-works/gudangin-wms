<x-guest-layout title="Login - GudangIn">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="space-y-6 bg-card p-8 rounded-2xl border border-border shadow-lg">
        <!-- Logo & Branding Header -->
        <div class="flex justify-center pt-2 pb-1">
            <img src="{{ asset('images/gudangin-logo.png') }}" alt="GudangIn" class="h-14 w-auto object-contain">
        </div>

        <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true" class="space-y-4">
            @csrf

            <!-- Username -->
            <x-form-input
                name="username"
                label="Username"
                type="text"
                :value="old('username')"
                required
                autofocus
                autocomplete="username"
                placeholder="Masukkan username Anda"
                :messages="$errors->get('username')"
            />

            <!-- Password -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <x-input-label for="password" :value="__('Password')" :required="true" />
                </div>
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-xs text-muted-foreground">{{ __('Ingat sesi login saya') }}</span>
                </label>
            </div>

            <div class="pt-2">
                <x-primary-button class="w-full justify-center py-2.5 bg-indigo-600 hover:bg-indigo-700" ::disabled="loading">
                    <svg x-show="loading" style="display: none;" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!loading">{{ __('Masuk ke GudangIn') }}</span>
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
