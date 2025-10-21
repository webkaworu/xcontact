<?php

namespace Database\Factories;

use App\Models\Form;
use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Form::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $notificationTemplate = EmailTemplate::where('type', 'notification')->inRandomOrder()->first() ?? EmailTemplate::factory()->create(['type' => 'notification']);
        $autoReplyTemplate = EmailTemplate::where('type', 'auto_reply')->inRandomOrder()->first() ?? EmailTemplate::factory()->create(['type' => 'auto_reply']);

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->sentence(3),
            'recipient_email' => $this->faker->unique()->safeEmail(),
            'notification_template_id' => $notificationTemplate->id,
            'auto_reply_enabled' => $this->faker->boolean(),
            'auto_reply_template_id' => $autoReplyTemplate->id,
            'daily_limit' => $this->faker->numberBetween(1, 100),
            'monthly_limit' => $this->faker->numberBetween(100, 1000),
        ];
    }
}
