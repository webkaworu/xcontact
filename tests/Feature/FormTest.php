<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\Form;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $normalUser;
    protected EmailTemplate $defaultNotificationTemplate;
    protected EmailTemplate $defaultAutoReplyTemplate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        // Create admin user
        $this->adminUser = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $this->adminUser->roles()->attach($adminRole);
        $this->adminUser->givePermissionTo('forms.manage');
        $this->adminUser->givePermissionTo('forms.create');

        // Create normal user
        $this->normalUser = User::factory()->create();
        $normalRole = Role::where('name', 'user')->first();
        $this->normalUser->roles()->attach($normalRole);
        $this->normalUser->givePermissionTo('forms.create');
        $this->normalUser->givePermissionTo('forms.view');

        // Create default email templates
        $this->defaultNotificationTemplate = EmailTemplate::factory()->create([
            'name' => 'Default Notification',
            'type' => 'notification',
            'is_default' => true,
        ]);
        $this->defaultAutoReplyTemplate = EmailTemplate::factory()->create([
            'name' => 'Default Auto Reply',
            'type' => 'auto_reply',
            'is_default' => true,
        ]);
    }

    /** @test */
    public function admin_can_list_all_forms(): void
    {
        Form::factory()->for($this->normalUser)->count(3)->create();
        $response = $this->actingAs($this->adminUser)->getJson('/api/forms');
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    ['id', 'user_id', 'name', 'recipient_email', 'notification_template_id', 'auto_reply_enabled', 'auto_reply_template_id', 'daily_limit', 'monthly_limit', 'created_at', 'updated_at']
                ]
            ]);
    }

    /** @test */
    public function normal_user_can_list_only_their_own_forms(): void
    {
        Form::factory()->for($this->normalUser)->count(2)->create();
        Form::factory()->for($this->adminUser)->count(1)->create();

        $response = $this->actingAs($this->normalUser)->getJson('/api/forms');
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.user_id', $this->normalUser->id);
    }

    /** @test */
    public function unauthorized_user_cannot_list_forms(): void
    {
        $response = $this->getJson('/api/forms');
        $response->assertStatus(401);
    }

    /** @test */
    public function user_with_forms_create_permission_can_create_form(): void
    {
        $formData = [
            'name' => 'My New Form',
            'recipient_email' => 'test@example.com',
            'auto_reply_enabled' => true,
        ];

        $response = $this->actingAs($this->normalUser)->postJson('/api/forms', $formData);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'My New Form'])
            ->assertJsonPath('data.user_id', $this->normalUser->id)
            ->assertJsonPath('data.notification_template_id', $this->defaultNotificationTemplate->id)
            ->assertJsonPath('data.auto_reply_template_id', $this->defaultAutoReplyTemplate->id);

        $this->assertDatabaseHas('forms', ['name' => 'My New Form', 'user_id' => $this->normalUser->id]);
    }

    /** @test */
    public function form_creation_limit_is_respected(): void
    {
        $this->normalUser->form_creation_limit = 1;
        $this->normalUser->save();

        Form::factory()->for($this->normalUser)->create(); // Create one form

        $formData = [
            'name' => 'Another Form',
            'recipient_email' => 'another@example.com',
        ];

        $response = $this->actingAs($this->normalUser)->postJson('/api/forms', $formData);
        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'Form creation limit reached.']);
    }

    /** @test */
    public function admin_can_create_form_even_if_user_has_limit(): void
    {
        $this->normalUser->form_creation_limit = 1;
        $this->normalUser->save();

        Form::factory()->for($this->normalUser)->create(); // Create one form

        $formData = [
            'name' => 'Admin Created Form',
            'recipient_email' => 'admin@example.com',
            'user_id' => $this->normalUser->id, // Admin creating for normal user
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/forms', $formData);
        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Admin Created Form']);
        $this->assertDatabaseHas('forms', ['name' => 'Admin Created Form', 'user_id' => $this->normalUser->id]);
    }

    /** @test */
    public function form_creation_validation_works(): void
    {
        $response = $this->actingAs($this->normalUser)->postJson('/api/forms', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'recipient_email']);
    }

    /** @test */
    public function admin_can_show_any_form(): void
    {
        $form = Form::factory()->for($this->normalUser)->create();
        $response = $this->actingAs($this->adminUser)->getJson('/api/forms/' . $form->id);
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $form->name]);
    }

    /** @test */
    public function normal_user_can_show_their_own_form(): void
    {
        $form = Form::factory()->for($this->normalUser)->create();
        $response = $this->actingAs($this->normalUser)->getJson('/api/forms/' . $form->id);
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $form->name]);
    }

    /** @test */
    public function normal_user_cannot_show_another_users_form(): void
    {
        $form = Form::factory()->for($this->adminUser)->create();
        $response = $this->actingAs($this->normalUser)->getJson('/api/forms/' . $form->id);
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_any_form(): void
    {
        $form = Form::factory()->for($this->normalUser)->create();
        $updatedData = ['name' => 'Updated Form Name'];
        $response = $this->actingAs($this->adminUser)->putJson('/api/forms/' . $form->id, $updatedData);
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Form Name']);
        $this->assertDatabaseHas('forms', ['id' => $form->id, 'name' => 'Updated Form Name']);
    }

    /** @test */
    public function normal_user_can_update_their_own_form(): void
    {
        $form = Form::factory()->for($this->normalUser)->create();
        $updatedData = ['name' => 'User Updated Form Name'];
        $response = $this->actingAs($this->normalUser)->putJson('/api/forms/' . $form->id, $updatedData);
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'User Updated Form Name']);
        $this->assertDatabaseHas('forms', ['id' => $form->id, 'name' => 'User Updated Form Name']);
    }

    /** @test */
    public function normal_user_cannot_update_another_users_form(): void
    {
        $form = Form::factory()->for($this->adminUser)->create();
        $updatedData = ['name' => 'Attempted Update'];
        $response = $this->actingAs($this->normalUser)->putJson('/api/forms/' . $form->id, $updatedData);
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_any_form(): void
    {
        $form = Form::factory()->for($this->normalUser)->create();
        $response = $this->actingAs($this->adminUser)->deleteJson('/api/forms/' . $form->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('forms', ['id' => $form->id]);
    }

    /** @test */
    public function normal_user_can_delete_their_own_form(): void
    {
        $form = Form::factory()->for($this->normalUser)->create();
        $response = $this->actingAs($this->normalUser)->deleteJson('/api/forms/' . $form->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('forms', ['id' => $form->id]);
    }

    /** @test */
    public function normal_user_cannot_delete_another_users_form(): void
    {
        $form = Form::factory()->for($this->adminUser)->create();
        $response = $this->actingAs($this->normalUser)->deleteJson('/api/forms/' . $form->id);
        $response->assertStatus(403);
    }
}
