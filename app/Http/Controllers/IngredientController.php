<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;


class IngredientController extends Controller
{

    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Ingredient::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $this->authorize('create',  Ingredient::class);
        $field = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('ingredients')],
            'tags' => 'nullable|array',
            'plat_ids' => 'required|array',
            'plat_ids.*' => 'exists:plats,id',
        ]);

        $ingredient = Ingredient::create([
            'name' => $field['name'],
            'tags' => $field['tags'] ?? [],
        ]);

        $ingredient->plats()->attach($field['plat_ids']);

        return response()->json([
            'message' => 'Ingredient created and attached to plats successfully',
            'ingredient' => $ingredient->load('plats')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ingredient $ingredient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ingredient $ingredient) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $this->authorize('update' , Ingredient::class);
        $field = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('ingredients')],
            'tags' => 'nullable|array',
            'plat_ids' => 'required|array',
            'plat_ids.*' => 'exists:plats,id',
        ]);

        $ingredient->update($field);

        return response()->json([
            'message' => 'Ingredient updated   successfully',
            'ingredient' => $ingredient->load('plats')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient)
    {

        $this->authorize('delete', $ingredient);
        $ingredient->delete();
        return ["message" => "plats deleted"];
    }
}
