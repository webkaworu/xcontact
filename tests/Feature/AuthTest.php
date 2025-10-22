<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Infrastructure\Persistence\Eloquent\User;
use App\Infrastructure\Persistence\Eloquent\RegistrationToken;
use App\Infrastructure\Persistence\Eloquent\Plan;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PlansSeeder::class);
    }

    /** @test */
    public function a_user_can_register_with_a_valid_registration_token()
    {
        $registrationToken = RegistrationToken::factory()->create();
        $password = 'password';

        $response = $this->postJson('/api/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $password,
            'password_confirmation' => $password,
            'registration_token' => $registrationToken->token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token',
                ],
            ]);

        $this->assertCount(2, User::all()); // RegistrationToken creates one user, plus the new user
    }

    /** @test */
    public function a_user_cannot_register_with_an_invalid_registration_token()
    {
        $password = 'password';

        $response = $this->postJson('/api/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $password,
            'password_confirmation' => $password,
            'registration_token' => 'invalid_token',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['registration_token']);

        $this->assertCount(0, User::all());
    }

    /** @test */
    public function a_user_can_login_with_valid_credentials()
    {
        $defaultPlan = Plan::where('name', '無料プラン')->first();
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
            'plan_id' => $defaultPlan->id,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token',
                ],
            ]);
    }

    /** @test */
    public function a_user_cannot_login_with_invalid_credentials()
    {
        $defaultPlan = Plan::where('name', '無料プラン')->first();
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'plan_id' => $defaultPlan->id,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function a_logged_in_user_can_logout()
    {
        $defaultPlan = Plan::where('name', '無料プラン')->first();
        $user = User::factory()->create([
            'plan_id' => $defaultPlan->id,
        ]);
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'ログアウトしました。',
            ]);

        $this->assertCount(0, $user->tokens);
    }
}
