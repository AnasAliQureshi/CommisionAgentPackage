<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the permissions
        $permissions = [
            'sales_targets.view',
            'sales_targets.create',
            'sales_targets.update',
            'sales_targets.delete',
            'sales_targets.commission_report',
        ];

        // Insert or retrieve permissions
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            );

            // Assign permission to Super Admin role
            $role = Role::find(1);
            if ($role) {
                $role->givePermissionTo($permission);
            } else {
                $this->command->error("Role with ID 1 not found.");
            }
        }
    }
}
