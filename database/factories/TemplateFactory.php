<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Template>
 */
class TemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => 'Template ' . $this->faker->word(),
            'subject' => $this->faker->sentence(3),
            'html_content' => '<p>' . $this->faker->sentence(8) . '</p>',
        ];
    }
}
