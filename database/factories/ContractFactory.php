<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $DAY = 60 * 60 * 24;
        $faker = $this->faker;
        $startsAt = strtotime($faker->date('d-m-Y'));
        $endsAt = $startsAt + ($faker->numberBetween(1, 15) * $DAY);

        $car = Car::inRandomOrder('id')->first();

        return [
            'daily_price' => $car->daily_price,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'client_id' => Client::inRandomOrder('id')->first()->id,
            'car_id' => $car->id,
        ];
    }
}
