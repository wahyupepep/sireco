<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'category-list',
            'category-create',
            'category-edit',
            'category-delete',
            'category-upload',
            'category-upload-delete',
            'menu-list',
            'menu-create',
            'menu-edit',
            'menu-delete',
            'menu-upload',
            'menu-upload-delete',
            'banner-list',
            'banner-create',
            'banner-edit',
            'banner-delete',
            'banner-upload',
            'banner-upload-delete',
            'slider-list',
            'slider-create',
            'slider-edit',
            'slider-delete',
            'slider-upload',
            'slider-upload-delete',
            'partner-list',
            'partner-create',
            'partner-edit',
            'partner-delete',
            'partner-upload',
            'partner-upload-delete',
            'setting-general-list',
            'setting-general-create',
            'setting-general-edit',
            'setting-general-delete',
            'setting-general-upload',
            'setting-general-upload-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
