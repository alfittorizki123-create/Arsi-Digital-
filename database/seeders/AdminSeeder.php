<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Bapenda',
                'email' => 'admin@bapenda.riau.go.id',
                'password' => Hash::make('password'),
            ]
        );
    }
}
