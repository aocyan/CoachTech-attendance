<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RegisterUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => '山田　太郎',
            'email' => 'user@example.com',
            'password' => bcrypt('1234abcd'),
            'status' => 'clockIn',
        ]);
    }
}
