<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
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
        $this->adminUser->givePermissionTo('templates.manage');

        $this->normalUser = User::factory()->create();
        $normalRole = Role::where('name', 'user')->first();
        $this->normalUser->roles()->attach($normalRole);
    }

    public function test_admin_can_list_email_templates(): void
    {
        EmailTemplate::factory()->count(3)->create();
        $response = $this->actingAs($this->adminUser)->getJson('/api/templates');
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'type', 'subject', 'body', 'is_default', 'created_at', 'updated_at']]]
        );
    }

    public function test_normal_user_cannot_list_email_templates(): void
    {
        EmailTemplate::factory()->count(3)->create();
        $response = $this->actingAs($this->normalUser)->getJson('/api/templates');
        $response->assertStatus(403);
    }

    public function test_admin_can_create_email_template(): void
    {
        $templateData = [
            'name' => 'Test Template',
            'type' => 'notification',
            'subject' => 'Test Subject',
            'body' => 'Test Body',
            'is_default' => false,
        ];
        $response = $this->actingAs($this->adminUser)->postJson('/api/templates', $templateData);
        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Template']);
        $this->assertDatabaseHas('email_templates', ['name' => 'Test Template']);
    }

    public function test_admin_can_show_email_template(): void
    {
        $template = EmailTemplate::factory()->create();
        $response = $this->actingAs($this->adminUser)->getJson('/api/templates/' . $template->id);
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $template->name]);
    }

    public function test_admin_can_update_email_template(): void
    {
        $template = EmailTemplate::factory()->create();
        $updatedData = [
            'name' => 'Updated Template',
            'type' => 'auto_reply',
            'subject' => 'Updated Subject',
            'body' => 'Updated Body',
            'is_default' => true,
        ];
        $response = $this->actingAs($this->adminUser)->putJson('/api/templates/' . $template->id, $updatedData);
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Template']);
        $this->assertDatabaseHas('email_templates', ['name' => 'Updated Template']);
    }

    public function test_admin_can_delete_email_template(): void
    {
        $template = EmailTemplate::factory()->create();
        $response = $this->actingAs($this->adminUser)->deleteJson('/api/templates/' . $template->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('email_templates', ['id' => $template->id]);
    }

    public function test_normal_user_cannot_create_email_template(): void
    {
        $templateData = [
            'name' => 'Test Template',
            'type' => 'notification',
            'subject' => 'Test Subject',
            'body' => 'Test Body',
            'is_default' => false,
        ];
        $response = $this->actingAs($this->normalUser)->postJson('/api/templates', $templateData);
        $response->assertStatus(403);
    }
}
