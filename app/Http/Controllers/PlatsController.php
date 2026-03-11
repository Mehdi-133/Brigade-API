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
        return Plats::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlatsRequest $request)
    {
        $field = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'category_id' => 'required|exists:categories,id'
        ]);

        $plats = Plats::create($field);
        return response()->json(['message' => 'plat created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Plats $plats)
    {
        return [
            'plat' => $plats,
            'category' => $plats->category
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlatsRequest $request, Plats $plats)
    {
        $field = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'category_id' => 'required|exists:categories,id'
        ]);

        $plats->update($field);
        return response()->json(['message' => 'plat updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plats $plats)
    {
        $plats->delete();
        return ["message" => "plats deleted"];
    }
}
