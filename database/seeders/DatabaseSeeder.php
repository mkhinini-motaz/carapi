<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Car;
use App\Models\Client;
use App\Models\Contract;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $agency = Agency::factory()->create([
            "name"=> "Msaken rent a car",
        ]);

        User::factory()->create([
            'first_name' => 'Test User',
            'last_name' => 'Test User',
            'password' => bcrypt('azerty'),
            'phone' => '+21623456109',
            'agency_id' => $agency->id,
        ]);

        Car::factory()->count(15)->create([
            'agency_id' => $agency->id,
        ]);

        Client::factory()->count(20)->create([
            'agency_id' => $agency->id,
        ]);

        Contract::factory()->count(15)->create();
    }
}
