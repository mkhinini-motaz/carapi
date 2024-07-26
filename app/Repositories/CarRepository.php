<?php

namespace App\Repositories;

use App\Models\Car;
use function App\Support\Helpers\fieldsLike;
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
     * Paginate shops
     *
     * @param int         $page
     * @param int         $perPage
     * @param string|null $search
     * @param string      $orderBy
     * @param string      $orderDirection
     *
     * @return LengthAwarePaginator
     */
    public static function paginate($page = 1, $perPage = 20, $search = null, $orderBy = 'created_at', $orderDirection = 'desc')
    {
        $cars = Car::query();

        if (isset($search)) {
            $cars->where(fieldsLike($search, 'name'));
        }

        if (isset($orderBy) && isset($orderDirection)) {
            $cars->orderBy($orderBy, $orderDirection);
        }

        return $cars->paginate($perPage, [ '*' ], 'page', $page);
    }
}