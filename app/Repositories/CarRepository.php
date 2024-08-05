<?php

namespace App\Repositories;

use App\Models\Car;

use function App\Support\Helpers\availabilityWithinPeriod;
use function App\Support\Helpers\fieldsLike;
use function App\Support\Helpers\occursInPeriod;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CarRepository
{
    /**
     * @var Car
     */
    private $car;

    /**
     * CarRepository constructor.
     *
     * @param Car $car
     */
    public function __construct(Car $car)
    {
        $this->car = $car;
    }

    /**
     * Paginate cars
     *
     * @param int         $page
     * @param int         $perPage
     * @param string|null $search
     * @param string      $orderBy
     * @param string      $orderDirection
     * @param int|null    $contractFreeOnPeriodStart
     * @param int|null    $contractFreeOnPeriodEnd
     *
     * @return LengthAwarePaginator
     */
    public static function paginate($page = 1, $perPage = 20, $search = null, $orderBy = 'created_at', $orderDirection = 'desc', $contractFreeOnPeriodStart = null, $contractFreeOnPeriodEnd = null)
    {
        $cars = Car::query();

        if (isset($contractFreeOnPeriodStart) && isset($contractFreeOnPeriodEnd)) {
            $cars->wherePossiblyAvailableInPeriod($contractFreeOnPeriodStart, $contractFreeOnPeriodEnd)
                ->withContractsInPeriod($contractFreeOnPeriodStart, $contractFreeOnPeriodEnd)
                ->orderByContractsCountForPeriod($contractFreeOnPeriodStart, $contractFreeOnPeriodEnd);
        }

        if (isset($search)) {
            $cars->where(fieldsLike($search, 'manufacturer', 'model_name', 'licence_plate'));
        }

        if (isset($orderBy) && isset($orderDirection)) {
            $cars->orderBy($orderBy, $orderDirection);
        }

        $cars = $cars->paginate($perPage, [ '*' ], 'page', $page);

        if (isset($contractFreeOnPeriodStart) && isset($contractFreeOnPeriodEnd)) {
            foreach ($cars as &$car) {
                $car->availability_in_period = $car->availabilityInPeriod($contractFreeOnPeriodStart, $contractFreeOnPeriodEnd);
            }
        }

        return $cars;
    }
}