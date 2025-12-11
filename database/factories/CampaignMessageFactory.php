<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CampaignMessage>
 */
class CampaignMessageFactory extends Factory
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
            'campaign_id' => \App\Models\Campaign::factory(),
            'contact_id' => \App\Models\Contact::factory(),
            'to_email' => $this->faker->safeEmail(),
            'message_id' => null,
            'sent_at' => null,
            'open_count' => 0,
            'first_open_at' => null,
            'click_count' => 0,
            'last_click_at' => null,
            'unsubscribe_at' => null,
            'unsubscribe_token' => \Illuminate\Support\Str::uuid()->toString(),
        ];
    }
}
