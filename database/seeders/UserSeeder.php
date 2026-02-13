<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username'   => 'superadmin',
                'password'   => Hash::make('superadmin123'),
                'full_name'  => 'Super Administrator',
                'role'       => 'superadmin',
                'email'      => 'superadmin@example.com',
                'phone'      => '081111111111',
                'is_active'  => 1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'username'   => 'admin',
                'password'   => Hash::make('admin123'),
                'full_name'  => 'Administrator',
                'role'       => 'admin',
                'email'      => 'admin@example.com',
                'phone'      => '082222222222',
                'is_active'  => 1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'username'   => 'teknik',
                'password'   => Hash::make('teknik123'),
                'full_name'  => 'Staff Teknik',
                'role'       => 'teknik',
                'email'      => 'teknik@example.com',
                'phone'      => '083333333333',
                'is_active'  => 1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'username'   => 'owner',
                'password'   => Hash::make('owner123'),
                'full_name'  => 'Owner',
                'role'       => 'owner',
                'email'      => 'owner@example.com',
                'phone'      => '084444444444',
                'is_active'  => 1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
        ]);
    }
}
