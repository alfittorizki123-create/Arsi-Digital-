<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'username' => 'admin',
                'name' => 'Admin Bapenda',
                'email' => 'admin@bapenda.riau.go.id',
                'password' => 'password',
            ],
            [
                'username' => 'admin2',
                'name' => 'Admin Bapenda 2',
                'email' => 'admin2@bapenda.riau.go.id',
                'password' => 'password',
            ],
            [
                'username' => 'admin3',
                'name' => 'Admin Bapenda 3',
                'email' => 'admin3@bapenda.riau.go.id',
                'password' => 'password',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['username' => $admin['username']],
                [
                    'name' => $admin['name'],
                    'email' => $admin['email'],
                    'password' => Hash::make($admin['password']),
                ]
            );
        }
    }
}
