<?php

namespace Database\Seeders;

use App\Models\Contract;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Contract::factory()->create([
            'starts_at' => strtotime('20-06-2025 18:00:00'),
            'ends_at' => strtotime('31-06-2025 18:00:00'),
            'car_id' => 1
        ]);

        Contract::factory()->create([
            'starts_at' => strtotime('10-07-2025 18:00:00'),
            'ends_at' => strtotime('15-07-2025 18:00:00'),
            'car_id' => 1
        ]);

        Contract::factory()->create([
            'starts_at' => strtotime('10-08-2025 18:00:00'),
            'ends_at' => strtotime('25-08-2025 18:00:00'),
            'car_id' => 1
        ]);

        Contract::factory()->create([
            'starts_at' => strtotime('25-08-2025 18:00:00'),
            'ends_at' => strtotime('07-09-2025 18:00:00'),
            'car_id' => 2
        ]);

        Contract::factory()->create([
            'starts_at' => strtotime('13-07-2025 18:00:00'),
            'ends_at' => strtotime('15-08-2025 18:00:00'),
            'car_id' => 2
        ]);
    }
}
