<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            EmailTemplateSeeder::class,
        ]);

        // Create Admin User
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $adminRole = Role::where('name', 'admin')->first();
        $adminUser->roles()->attach($adminRole);

        // Create General User
        $generalUser = User::factory()->create([
            'name' => 'General User',
            'email' => 'user@example.com',
        ]);
        $userRole = Role::where('name', 'user')->first();
        $generalUser->roles()->attach($userRole);
    }
}