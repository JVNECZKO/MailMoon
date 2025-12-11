<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
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
            'sending_identity_id' => \App\Models\SendingIdentity::factory(),
            'contact_list_id' => \App\Models\ContactList::factory(),
            'template_id' => \App\Models\Template::factory(),
            'name' => 'Campaign ' . $this->faker->word(),
            'subject' => $this->faker->sentence(4),
            'html_content' => '<p>' . $this->faker->paragraph() . '</p>',
            'track_opens' => true,
            'track_clicks' => true,
            'enable_unsubscribe' => true,
            'send_interval_seconds' => 1,
            'status' => 'draft',
            'scheduled_at' => now(),
        ];
    }
}
