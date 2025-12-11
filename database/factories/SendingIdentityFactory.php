<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SendingIdentity>
 */
class SendingIdentityFactory extends Factory
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
            'name' => $this->faker->company() . ' Mailer',
            'from_email' => $this->faker->companyEmail(),
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => 587,
            'smtp_username' => $this->faker->userName(),
            'smtp_password' => 'secret',
            'smtp_encryption' => 'tls',
            'is_active' => true,
        ];
    }
}
