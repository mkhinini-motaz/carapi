<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone' => $this->faker->phoneNumber(),
            'gender' => $this->faker->numberBetween(1, 2),
            'birth_date' => $this->faker->date('d/m/Y'),
            'id_number' => $this->faker->creditCardNumber(),
            'driver_licence_number' => $this->faker->swiftBicNumber(),
            'driver_licence_delivery_date' => $this->faker->date('d/m/Y'),
        ];
    }
}
