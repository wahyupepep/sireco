<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'fullname' => 'Abdurahman',
            'name' => 'abdurahman',
            'email' => 'abdurahmanrizal1@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => date('Y-m-d H:i:s'),
            'role' => 4,
            'status' => 1
        ]);

        $role = Role::where(['name' => 'Member'])->first();

        $permissions = Permission::pluck('id', 'id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
