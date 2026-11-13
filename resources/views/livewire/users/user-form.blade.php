<x-modal name="user-form-modal" :title="''" maxWidth="2xl">
    <div class="p-6">
        <!-- Custom Header -->
        <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
            <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                {{ $isEditing ? 'Edit User GudangIn' : 'Tambah User GudangIn Baru' }}
            </h3>
            <p class="text-sm text-muted-foreground">
                {{ $isEditing ? 'Perbarui informasi profil dan hak akses pengguna.' : 'Daftarkan pengguna baru untuk akses sistem pergudangan.' }}
            </p>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name -->
                <x-form-input
                    name="name"
                    label="Nama Lengkap"
                    type="text"
                    wire:model="name"
                    required
                    placeholder="Contoh: Budi Santoso"
                />

                <!-- Username -->
                <x-form-input
                    name="username"
                    label="Username"
                    type="text"
                    wire:model="username"
                    required
                    placeholder="Contoh: budi_gudang"
                />
            </div>

            <!-- Email -->
            <x-form-input
                name="email"
                label="Alamat Email"
                type="email"
                wire:model="email"
                required
                placeholder="email@gudangin.com"
            />

            <!-- Role & Jabatan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <x-input-label for="role" :value="__('Role & Privilege')" />
                    <select id="role" wire:model="role" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="basic_user">Basic User (Warehouse Operator)</option>
                        <option value="super_admin">Super Admin (Full System Access)</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="jabatan" :value="__('Jabatan / Posisi')" />
                    <x-text-input
                        id="jabatan"
                        name="jabatan"
                        type="text"
                        wire:model="jabatan"
                        required
                        placeholder="Contoh: Staff Inbound / Kepala Gudang"
                    />
                    <x-input-error :messages="$errors->get('jabatan')" />
                </div>
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        wire:model="password"
                        :required="!$isEditing"
                        autocomplete="new-password"
                        placeholder="{{ $isEditing ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}"
                    />
                    <x-input-error :messages="$errors->get('password')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                    <x-text-input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        wire:model="password_confirmation"
                        :required="!$isEditing"
                        autocomplete="new-password"
                    />
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-4">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'user-form-modal' })">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-primary-button type="submit" wire:loading.attr="disabled">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-heroicon-o-check wire:loading.remove wire:target="save" class="w-4 h-4 mr-2" />
                    {{ $isEditing ? __('Simpan Perubahan') : __('Buat User') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
