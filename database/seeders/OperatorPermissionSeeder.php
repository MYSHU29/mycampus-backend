<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class OperatorPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Activity Log Permissions
            ['nama_permission' => 'activity-log.view', 'modul' => 'Operator', 'aksi' => 'View Log'],
            ['nama_permission' => 'activity-log.export', 'modul' => 'Operator', 'aksi' => 'Export Log'],
            ['nama_permission' => 'activity-log.delete', 'modul' => 'Operator', 'aksi' => 'Delete Old Log'],

            // User Management Permissions
            ['nama_permission' => 'user.view', 'modul' => 'Operator', 'aksi' => 'View User'],
            ['nama_permission' => 'user.create', 'modul' => 'Operator', 'aksi' => 'Create User'],
            ['nama_permission' => 'user.edit', 'modul' => 'Operator', 'aksi' => 'Edit User'],
            ['nama_permission' => 'user.delete', 'modul' => 'Operator', 'aksi' => 'Delete User'],
            ['nama_permission' => 'user.manage', 'modul' => 'Operator', 'aksi' => 'Manage User'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }

        echo "Operator permissions seeded successfully!\n";
    }
}
