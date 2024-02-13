<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Rizgi Saputra',
            'email' => 'rizgisaputra326@gmail.com',
            'password' => Hash::make('rizgi123'),
            'addres' => 'Jln. Masjid Nurul Fajri',
            'no_hp' => '0895379254459',
            'role' => 'admin'
        ]);
    }
}
