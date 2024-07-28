<?php

namespace App\Models;

use App\Support\DoesntConvertDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use HasFactory, SoftDeletes, DoesntConvertDates;

    protected $dateFormat = 'U';

    const TRANSMISSION_TYPE_MANUAL = 1;
    const TRANSMISSION_TYPE_AUTO = 2;
    const TRANSMISSION_TYPES = [ self::TRANSMISSION_TYPE_MANUAL, self::TRANSMISSION_TYPE_AUTO ];

    const FUEL_TYPE_GAS = 1;
    const FUEL_TYPE_PETROL = 2;
    const FUEL_TYPES = [ self::FUEL_TYPE_GAS, self::FUEL_TYPE_PETROL ];

    const TYPE_COMPACT = 1;
    const TYPE_SEDAN = 2;
    const TYPES = [ self::TYPE_COMPACT, self::TYPE_SEDAN ];

    public function contracts()
    {
        return $this->hasMany(Contract::class)
            ->orderBy('starts_at', 'desc');
    }
}
