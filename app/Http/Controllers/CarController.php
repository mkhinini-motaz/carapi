<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Http\Resources\CarResource;
use App\Http\Resources\ResourceCollection;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Repositories\CarRepository;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page           = $request->input('page', 1);
        $perPage        = $request->input('perPage', 20);
        $orderBy        = $request->input('orderBy', 'created_at');
        $search         = $request->input('search', null);
        $startsAt       = (int) $request->input('starts_at', null);
        $endsAt         = (int) $request->input('ends_at', null);
        $orderDirection = $request->input('orderDirection', 'desc');

        $cars = CarRepository::paginate($page, $perPage, $search, $orderBy, $orderDirection, $startsAt, 
        $endsAt);

        return ResourceCollection::jsonResponse($cars);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($carId)
    {
        $car = Car::with('contracts.client')
            ->withSum('contracts as total_price', \DB::raw('(contracts.daily_price * (ends_at - starts_at) / (60 * 60 * 24))'))
            ->findOrFail($carId);
        return new CarResource($car);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Car $car)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCarRequest $request, Car $car)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car)
    {
        //
    }
}
