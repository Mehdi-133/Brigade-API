<?php

namespace App\Http\Controllers;

use App\Models\Plats;
use App\Http\Requests\StorePlatsRequest;
use App\Http\Requests\UpdatePlatsRequest;

class PlatsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlatsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Plats $plats)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlatsRequest $request, Plats $plats)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plats $plats)
    {
        //
    }
}
