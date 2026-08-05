<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'name' => 'Admin Bapenda',
                'email' => 'admin@bapenda.riau.go.id',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'username' => 'staff',
                'name' => 'Staff Arsip 1',
                'email' => 'staff@bapenda.riau.go.id',
                'password' => 'password',
                'role' => 'staff',
            ],
            [
                'username' => 'staff2',
                'name' => 'Staff Arsip 2',
                'email' => 'staff2@bapenda.riau.go.id',
                'password' => 'password',
                'role' => 'staff',
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['username' => $u['username']],
                [
                    'name' => $u['name'],
                    'email' => $u['email'],
                    'password' => Hash::make($u['password']),
                    'role' => $u['role'],
                ]
            );
        }
    }
}
