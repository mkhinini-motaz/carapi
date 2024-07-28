<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Http\Resources\ResourceCollection;
use App\Models\Contract;
use App\Repositories\ContractRepository;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page           = $request->input('page', 1);
        $search         = $request->input('search', null);
        $endsAt         = $request->input('ends_at', null);
        $perPage        = $request->input('perPage', 20);
        $startsAt       = $request->input('starts_at', null);
        $orderBy        = $request->input('orderBy', 'starts_at');
        $orderDirection = $request->input('orderDirection', 'desc');

        $agency = auth()->user()->agency;
        $contracts = ContractRepository::paginate($agency, $page, $perPage, $search, $orderBy, $orderDirection, $startsAt, $endsAt);

        return ResourceCollection::jsonResponse($contracts);
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
    public function store(StoreContractRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractRequest $request, Contract $contract)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract)
    {
        //
    }
}
