<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insertOrIgnore([
            'name'                   => 'TechBits Admin',
            'email'                  => 'admin@techbits.lk',
            'phone'                  => '+94771234567',
            'password'               => Hash::make('Admin@1234'),
            'role'                   => 'admin',
            'status'                 => 'active',
            'failed_login_attempts'  => 0,
            'locked_until'           => null,
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);
    }
}
