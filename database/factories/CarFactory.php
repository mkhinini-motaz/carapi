<?php

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    const CAR_MODEL = [
        'Mercedes' => [ 'E250', 'S350', 'A200', 'E300 break', 'GLE' ],
        'BMW' => [ 'Serie 3', 'Serie 5', 'M5', 'M3' ],
        'Volkswagen' => [ 'Golf 8', 'Passat', 'UP', 'Tiguan' ],
        'Renault' => [ 'Clio 5', 'Megane' ],
        'Skoda' => [ 'Fabia', 'Octavia' ],
    ];
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = $this->faker;
        $manufacturer = $faker->randomElement(array_keys(self::CAR_MODEL));
        $modelYear = $faker->numberBetween(date('Y') - 3, (int) date('Y'));

        return [
            'agency_id' => 1,
            'daily_price' => $faker->numberBetween(7, 15) * 10000,
            'licence_plate' => $faker->numberBetween(240, 246) .
                ' TN ' . $faker->numberBetween(1111, 9876),
            'serial_number' => strtoupper($faker->bothify('??##?#?#?#????#####')),
            'manufacturer' => $manufacturer,
            'model_name' => $faker->randomElement(self::CAR_MODEL[$manufacturer]),
            'model_year' => $modelYear,
            'fiscal_horse_power' => $faker->numberBetween(4, 8),
            'number_of_cylinders' => $faker->numberBetween(3, 4),
            'transmission_type' => $faker->randomElement(Car::TRANSMISSION_TYPES),
            'fuel_type' => $faker->randomElement(Car::FUEL_TYPES),
            'type' => $faker->randomElement(Car::TYPES),
            'first_circulation_date' => $modelYear . '/' .$faker->numberBetween(1, 12) . '/' . $faker->numberBetween(1,28),
            'circulation_tax_ends_at' => strtotime($faker->numberBetween($modelYear + 2, $modelYear + 4) . '/' .$faker->numberBetween(1, 12) . '/' . $faker->numberBetween(1,28)),
            'insurance_ends_at' => strtotime($faker->numberBetween($modelYear + 2, $modelYear + 4) . '/' .$faker->numberBetween(1, 12) . '/' . $faker->numberBetween(1,28)),
            'technical_control_ends_at' => strtotime($faker->numberBetween($modelYear + 2, $modelYear + 4) . '/' .$faker->numberBetween(1, 12) . '/' . $faker->numberBetween(1,28)),
        ];
    }
}
