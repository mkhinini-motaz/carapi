<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            'agency_id' => 1,
            'daily_price' => $this->daily_price,
            'licence_plate' => $this->licence_plate,
            'serial_number' => $this->serial_number,
            'manufacturer' => $this->manufacturer,
            'model_name' => $this->model_name,
            'model_year' => $this->model_year,
            'fiscal_horse_power' => $this->fiscal_horse_power,
            'number_of_cylinders' => $this->number_of_cylinders,
            'transmission_type' => $this->transmission_type,
            'fuel_type' => $this->fuel_type,
            'type' => $this->type,
            'first_circulation_date' => $this->first_circulation_date,
            'circulation_tax_ends_at' => $this->circulation_tax_ends_at,
            'insurance_ends_at' => $this->insurance_ends_at,
            'technical_control_ends_at' => $this->technical_control_ends_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
