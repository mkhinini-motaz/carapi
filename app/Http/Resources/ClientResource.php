<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'id_number' => $this->id_number,
            'driver_licence_number' => $this->driver_licence_number,
            'driver_licence_delivery_date' => $this->driver_licence_delivery_date,
            'birth_date' => $this->birth_date,
            'agency_id' => $this->agency_id,
            'created_at' => $this->created_at,
            'updated_at'=> $this->updated_at,
        ];
    }
}
