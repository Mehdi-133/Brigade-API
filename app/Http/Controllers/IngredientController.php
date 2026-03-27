<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class IngredientController extends Controller
{
    use AuthorizesRequests;

    #[OA\Get(
        path: "/api/ingredient",
        summary: "Get all ingredients",
        security: [["bearerAuth" => []]],
        tags: ["Ingredients"],
        responses: [
            new OA\Response(response: 200, description: "List of ingredients"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function index()
    {
        return Ingredient::all();
    }

    public function create() {}

    #[OA\Post(
        path: "/api/ingredient",
        summary: "Create new ingredient",
        security: [["bearerAuth" => []]],
        tags: ["Ingredients"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "plat_ids"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Tomato"),
                    new OA\Property(property: "tags", type: "array", items: new OA\Items(type: "string"), example: ["vegan", "gluten_free"]),
                    new OA\Property(property: "plat_ids", type: "array", items: new OA\Items(type: "integer"), example: [1, 2])
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Ingredient created successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Unauthorized")
        ]
    )]
    public function store(Request $request)
    {
        $this->authorize('create', Ingredient::class);
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

    #[OA\Get(
        path: "/api/ingredient/{id}",
        summary: "Get ingredient by ID",
        security: [["bearerAuth" => []]],
        tags: ["Ingredients"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Ingredient details"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Ingredient not found")
        ]
    )]
    public function show(Ingredient $ingredient)
    {
        return $ingredient;
    }

    public function edit(Ingredient $ingredient) {}

    #[OA\Put(
        path: "/api/ingredient/{id}",
        summary: "Update ingredient",
        security: [["bearerAuth" => []]],
        tags: ["Ingredients"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "plat_ids"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Tomato"),
                    new OA\Property(property: "tags", type: "array", items: new OA\Items(type: "string"), example: ["vegan"]),
                    new OA\Property(property: "plat_ids", type: "array", items: new OA\Items(type: "integer"), example: [1])
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Ingredient updated successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Unauthorized"),
            new OA\Response(response: 404, description: "Ingredient not found")
        ]
    )]
    public function update(Request $request, Ingredient $ingredient)
    {
        $this->authorize('update', Ingredient::class);
        $field = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('ingredients')],
            'tags' => 'nullable|array',
            'plat_ids' => 'required|array',
            'plat_ids.*' => 'exists:plats,id',
        ]);

        $ingredient->update($field);

        return response()->json([
            'message' => 'Ingredient updated successfully',
            'ingredient' => $ingredient->load('plats')
        ], 200);
    }

    #[OA\Delete(
        path: "/api/ingredient/{id}",
        summary: "Delete ingredient",
        security: [["bearerAuth" => []]],
        tags: ["Ingredients"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Ingredient deleted"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Unauthorized"),
            new OA\Response(response: 404, description: "Ingredient not found")
        ]
    )]
    public function destroy(Ingredient $ingredient)
    {
        $this->authorize('delete', $ingredient);
        $ingredient->delete();
        return ["message" => "ingredient deleted"];
    }
}
