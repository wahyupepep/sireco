<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'superadmin',
            'email' => 'admin@purwatirtadharma.com',
            'password' => bcrypt('password'),
            'email_verified_at' => date('Y-m-d H:i:s'),
            'role' => 1, // super admin
            'status' => '1', // aktif
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
