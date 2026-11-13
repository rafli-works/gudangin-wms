<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin Account
        User::updateOrCreate(
            ['email' => 'admin@gudangin.com'],
            [
                'name' => 'Super Administrator',
                'username' => 'superadmin',
                'email' => 'admin@gudangin.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'jabatan' => 'Kepala Gudang & Super Admin',
                'is_active' => true,
            ]
        );

        // Also update existing 'admin@admin.com' if present
        User::where('email', 'admin@admin.com')->update([
            'role' => 'super_admin',
            'jabatan' => 'Super Admin',
            'is_active' => true,
        ]);

        // 2. Basic User Account
        User::updateOrCreate(
            ['email' => 'staff@gudangin.com'],
            [
                'name' => 'Budi Santoso',
                'username' => 'staffgudang',
                'email' => 'staff@gudangin.com',
                'password' => Hash::make('password'),
                'role' => 'basic_user',
                'jabatan' => 'Staff Inbound & Outbound',
                'is_active' => true,
            ]
        );
    }
}
