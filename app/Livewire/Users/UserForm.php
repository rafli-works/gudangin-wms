<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use App\DTOs\UserData;
use App\Services\UserService;
use Illuminate\Validation\Rule;

class UserForm extends Component
{
    public ?User $user = null;
    public bool $isEditing = false;

    public $name;
    public $username;
    public $email;
    public $role = 'basic_user';
    public $jabatan = 'Warehouse Staff';
    public $password;
    public $password_confirmation;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($this->user?->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user?->id)],
            'role' => ['required', 'in:super_admin,basic_user'],
            'jabatan' => ['required', 'string', 'max:255'],
            'password' => [$this->isEditing ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ];
    }

    #[On('create-user')]
    public function create(): void
    {
        $this->resetValidation();
        $this->reset(['user', 'isEditing', 'name', 'username', 'email', 'password', 'password_confirmation']);
        $this->role = 'basic_user';
        $this->jabatan = 'Warehouse Staff';
        $this->dispatch('open-modal', name: 'user-form-modal');
    }

    #[On('edit-user')]
    public function edit(User $user): void
    {
        $this->resetValidation();
        $this->user = $user;
        $this->isEditing = true;

        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = $user->role ?? 'basic_user';
        $this->jabatan = $user->jabatan ?? 'Warehouse Staff';
        $this->password = '';
        $this->password_confirmation = '';

        $this->dispatch('open-modal', name: 'user-form-modal');
    }

    public function save(UserService $service): void
    {
        $this->validate();

        $data = new UserData(
            name: $this->name,
            username: $this->username,
            email: $this->email,
            password: $this->password ?: null,
            role: $this->role,
            jabatan: $this->jabatan,
        );

        try {
            if ($this->isEditing && $this->user) {
                $service->updateUser($this->user, $data);
                $message = 'User berhasil diperbarui.';
            } else {
                $service->createUser($data);
                $message = 'User baru berhasil ditambahkan.';
            }

            $this->dispatch('close-modal', name: 'user-form-modal');
            $this->dispatch('pg:eventRefresh-user-table');
            $this->dispatch('toast', message: $message, type: 'success');

            $this->reset(['user', 'isEditing', 'name', 'username', 'email', 'password', 'password_confirmation']);
            $this->role = 'basic_user';
            $this->jabatan = 'Warehouse Staff';

        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.users.user-form');
    }
}
