<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat peran/Role
        $adminRole = Role::create(['name' => 'admin']);
        $editorRole = Role::create(['name' => 'editor']);
        $userRole = Role::create(['name' => 'member']);

        // Membuat izin Default
        $createUserPermission = Permission::create(['name' => 'create-user']);
        $editUserPermission = Permission::create(['name' => 'edit-user']);
        $deleteUserPermission = Permission::create(['name' => 'delete-user']);

        $editPermission = Permission::create(['name' => 'edit-permission']);

        $viewPostPermission = Permission::create(['name' => 'view-post']);
        $createPostPermission = Permission::create(['name' => 'create-post']);
        $editPostPermission = Permission::create(['name' => 'edit-post']);
        $deletePostPermission = Permission::create(['name' => 'delete-post']);

        // Mengaitkan izin dengan peran
        $adminRole->givePermissionTo([
            $createUserPermission, $editUserPermission, $deleteUserPermission,
            $editPermission,
            $viewPostPermission, $createPostPermission, $editPostPermission, $deletePostPermission,
        ]);
        $editorRole->givePermissionTo([
            $viewPostPermission, $createPostPermission, $editPostPermission, $deletePostPermission
        ]);
        $userRole->givePermissionTo([
            $viewPostPermission, $createPostPermission
        ]);
    }
}
