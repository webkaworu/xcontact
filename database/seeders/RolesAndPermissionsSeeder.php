<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'users.manage',
            'users.view',
            'roles.manage',
            'forms.create',
            'forms.manage',
            'inquiries.view',
            'templates.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'description' => 'System Administrator']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'description' => 'General User']);

        // Assign all permissions to admin role
        $adminRole->permissions()->sync(Permission::all()->pluck('id'));

        // Assign specific permissions to user role
        $userRole->permissions()->sync([
            Permission::where('name', 'forms.create')->first()->id,
            Permission::where('name', 'forms.manage')->first()->id,
            Permission::where('name', 'inquiries.view')->first()->id,
        ]);
    }
}