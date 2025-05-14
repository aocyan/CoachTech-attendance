<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'name' => '鈴木　次郎',
            'email' => 'admin@example.com',
            'password' => bcrypt('1234abcd'),
        ]);
    }
}
