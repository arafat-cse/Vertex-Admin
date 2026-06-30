<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create default Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@vertex.dev'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('password'),
                'status'   => 'active',
            ]
        );

        $admin->assignRole('Super Admin');

        // Create 10 fake users
        $fakeUsers = User::factory()->count(10)->create([
            'status' => 'active',
        ]);

        foreach ($fakeUsers as $fakeUser) {
            $fakeUser->assignRole('User');
        }
    }
}
