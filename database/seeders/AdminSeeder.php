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
            ['email' => 'admin@lostfoundpolmed.test'],
            [
                'name'     => 'Admin LostFound Polmed',
                'password' => Hash::make('admin12345'),
                'role'     => 'admin',
            ]
        );
    }
}
