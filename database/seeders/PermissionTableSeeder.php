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
            'frontdesk-list',
            'frontdesk-create',
            'reservation-list',
            'reservation-order',
            'reservation-confirm-order',
            'reservation-order-summary',
            'reservation-list-order',
            'reservation-detail-order',
            'reservation-payment',
            'reservation-upload-payment',
            'sales',
            'sales-income',
            'member-list',
            'member-detail',
            'member-search',
            'verification-list',
            'verification-detail-order',
            'verification-order',
            'setting-list',
            'setting-profile',
            'setting-password',
            'setting-change-password',
            'setting-update-profile',
            'master-list',
            'master-payment-method',
            'master-payment-method-create',
            'master-payment-method-edit',
            'master-payment-method-update',
            'master-payment-method-delete',
            'master-room',
            'master-room-create',
            'master-room-edit',
            'master-room-update',
            'master-room-delete',
            'master-category-member',
            'master-category-member-create',
            'master-category-member-edit',
            'master-category-member-update',
            'master-category-member-delete',
            'master-discount',
            'master-discount-create',
            'master-discount-edit',
            'master-discount-update',
            'master-discount-delete',
            'master-user',
            'master-user-create',
            'master-user-edit',
            'master-user-update',
            'master-user-delete',
            'master-role',
            'master-role-create',
            'master-role-edit',
            'master-role-update',
            'master-role-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
