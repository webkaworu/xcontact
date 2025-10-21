<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $normalUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->adminUser = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $this->adminUser->roles()->attach($adminRole);

        $this->normalUser = User::factory()->create();
        $normalRole = Role::where('name', 'user')->first();
        $this->normalUser->roles()->attach($normalRole);
    }

    public function test_admin_can_list_roles(): void
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/roles');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'description', 'created_at', 'updated_at']]])
            ->assertJsonCount(2, 'data'); // admin and user roles from seeder
    }

    public function test_normal_user_cannot_list_roles(): void
    {
        $response = $this->actingAs($this->normalUser)->getJson('/api/roles');
        $response->assertStatus(403);
    }

    public function test_admin_can_create_role(): void
    {
        $permission = Permission::first();
        $response = $this->actingAs($this->adminUser)->postJson('/api/roles', [
            'name' => 'editor',
            'description' => 'Editor role',
            'permissions' => [$permission->id],
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'editor']);
        $this->assertDatabaseHas('roles', ['name' => 'editor']);
        $this->assertDatabaseHas('permission_role', [
            'role_id' => $response['data']['id'],
            'permission_id' => $permission->id,
        ]);
    }

    public function test_admin_can_show_role(): void
    {
        $role = Role::where('name', 'admin')->first();
        $response = $this->actingAs($this->adminUser)->getJson('/api/roles/' . $role->id);
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'admin'])
            ->assertJsonStructure(['data' => ['permissions']]);
    }

    public function test_admin_can_update_role(): void
    {
        $role = Role::where('name', 'user')->first();
        $permission = Permission::first();
        $response = $this->actingAs($this->adminUser)->putJson('/api/roles/' . $role->id, [
            'name' => 'subscriber',
            'description' => 'Subscriber role',
            'permissions' => [$permission->id],
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'subscriber']);
        $this->assertDatabaseHas('roles', ['name' => 'subscriber']);
        $this->assertDatabaseMissing('roles', ['name' => 'user']);
        $this->assertDatabaseHas('permission_role', [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);
    }

    public function test_admin_can_delete_role(): void
    {
        $role = Role::where('name', 'user')->first();
        $response = $this->actingAs($this->adminUser)->deleteJson('/api/roles/' . $role->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('roles', ['name' => 'user']);
    }

    public function test_admin_can_list_permissions(): void
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/permissions');
        $response->assertStatus(200)
            ->assertJsonStructure([['id', 'name', 'description', 'created_at', 'updated_at']]);
    }

    public function test_normal_user_cannot_list_permissions(): void
    {
        $response = $this->actingAs($this->normalUser)->getJson('/api/permissions');
        $response->assertStatus(403);
    }

    public function test_admin_can_get_user_roles(): void
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/users/' . $this->normalUser->id . '/roles');
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'user']);
    }

    public function test_admin_can_update_user_roles(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $response = $this->actingAs($this->adminUser)->putJson('/api/users/' . $this->normalUser->id . '/roles', [
            'roles' => [$adminRole->id],
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'admin']);
        $this->assertTrue($this->normalUser->fresh()->hasRole('admin'));
    }

    public function test_normal_user_cannot_update_user_roles(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $response = $this->actingAs($this->normalUser)->putJson('/api/users/' . $this->adminUser->id . '/roles', [
            'roles' => [$adminRole->id],
        ]);
        $response->assertStatus(403);
    }
}
