<?php

namespace App\Models;

use App\Support\DoesntConvertDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use function App\Support\Helpers\availabilityWithinPeriod;
use function App\Support\Helpers\occursInPeriod;

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
        return $this->hasMany(Contract::class);
    }

    public function scopeOrderByContractsCountForPeriod($query, $periodStart, $periodEnd)
    {
        return $query->withCount(
            [ 'contracts' => occursInPeriod('starts_at', 'ends_at', $periodStart, $periodEnd, true, false) ]
            )->orderBy('contracts_count', 'asc');
    }

    public function scopeWithContractsInPeriod($mainQuery, $periodStart, $periodEnd)
    {
        return $mainQuery->with('contracts', function($query) use ($periodStart, $periodEnd) {
            $query->where(occursInPeriod('starts_at', 'ends_at', $periodStart, $periodEnd, true, false))
                ->orderBy('starts_at', 'asc');
        });
    }

    public function scopeWherePossiblyAvailableInPeriod($mainQuery, $periodStart, $periodEnd)
    {

        return $mainQuery->where(function($query) use ($periodStart, $periodEnd) {
            $query->whereDoesntHave(
                'contracts', occursInPeriod('starts_at', 'ends_at', $periodStart, $periodEnd, true, true)
            )->orWhereHas(
                'contracts',  occursInPeriod('starts_at', 'ends_at', $periodStart, $periodEnd, true, false)
            );
        });
    }

    public function availabilityInPeriod($periodStart, $periodEnd)
    {
        if ($this->relationLoaded('contracts') && 0 === $this->contracts->count()) {
            return [[ $periodStart, $periodEnd ]];
        }

        $this->contracts->sortBy('starts_at');

        $currentStart = $periodStart;
        $availablity = [];
        foreach ($this->contracts as $contract) {
            if ($contract->starts_at > $periodEnd) {
                break;
            }

            if ($contract->starts_at > $currentStart) {
                $availablity[] = [$currentStart, $contract->starts_at];
            }

            $currentStart = max($currentStart, $contract->ends_at);
        }

        if ($currentStart < $periodEnd) {
            $availablity[] = [$currentStart, $periodEnd];
        }

        return $availablity;
    }
}
