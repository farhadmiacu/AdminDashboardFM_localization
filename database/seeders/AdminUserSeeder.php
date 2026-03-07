<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Users
        $users = [
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ],
            [
                'name' => 'Manager1',
                'username' => 'manager1',
                'email' => 'manager1@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'manager',
            ],
            [
                'name' => 'Manager2',
                'username' => 'manager2',
                'email' => 'manager2@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'manager',
            ],
            [
                'name' => 'Manager3',
                'username' => 'manager3',
                'email' => 'manager3@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'manager',
            ],
            [
                'name' => 'User1',
                'username' => 'user1',
                'email' => 'user1@gmail.com',
                'password' => Hash::make('12345678'),
            ],
            [
                'name' => 'User2',
                'username' => 'user2',
                'email' => 'user2@gmail.com',
                'password' => Hash::make('12345678'),
            ],
            [
                'name' => 'User3',
                'username' => 'user3',
                'email' => 'user3@gmail.com',
                'password' => Hash::make('12345678'),
            ],

        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']], // condition
                $user                        // data to update or insert
            );
        }
    }
}
