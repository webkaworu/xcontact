<?php

namespace Database\Factories;

use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word . ' Template',
            'type' => $this->faker->randomElement(['notification', 'auto_reply']),
            'subject' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
            'is_default' => false,
        ];
    }
}
