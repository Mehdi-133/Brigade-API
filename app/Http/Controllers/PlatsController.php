<?php

namespace App\Http\Controllers;

use App\Models\Plats;
use App\Http\Requests\StorePlatsRequest;
use App\Http\Requests\UpdatePlatsRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use OpenApi\Attributes as OA;

class PlatsController extends Controller
{
    use AuthorizesRequests;

    #[OA\Get(
        path: "/api/plats",
        summary: "Get all plats",
        security: [["bearerAuth" => []]],
        tags: ["Plats"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of plats"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            )
        ]
    )]
    public function index()
    {
        return Plats::all();
    }

    #[OA\Post(
        path: "/api/plats",
        summary: "Create new plat",
        security: [["bearerAuth" => []]],
        tags: ["Plats"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "description", "price", "category_id"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Pizza Margherita"),
                    new OA\Property(property: "description", type: "string", example: "Classic Italian pizza"),
                    new OA\Property(property: "price", type: "number", example: 12.99),
                    new OA\Property(property: "image", type: "string", example: "pizza.jpg"),
                    new OA\Property(property: "category_id", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Plat created successfully"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            ),
            new OA\Response(
                response: 403,
                description: "Unauthorized"
            )
        ]
    )]
    public function store(StorePlatsRequest $request)
    {
            
        $this->authorize('create', Plats::class);
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

    #[OA\Get(
        path: "/api/plats/{id}",
        summary: "Get plat by ID",
        security: [["bearerAuth" => []]],
        tags: ["Plats"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Plat details with category"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            ),
            new OA\Response(
                response: 404,
                description: "Plat not found"
            )
        ]
    )]
    public function show(Plats $plats)
    {
        return [
            'plat' => $plats,
            'category' => $plats->category
        ];
    }

    #[OA\Put(
        path: "/api/plats/{id}",
        summary: "Update plat",
        security: [["bearerAuth" => []]],
        tags: ["Plats"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "description", "price", "category_id"],
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "description", type: "string"),
                    new OA\Property(property: "price", type: "number"),
                    new OA\Property(property: "image", type: "string"),
                    new OA\Property(property: "category_id", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Plat updated successfully"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            ),
            new OA\Response(
                response: 403,
                description: "Unauthorized"
            ),
            new OA\Response(
                response: 404,
                description: "Plat not found"
            )
        ]
    )]
    public function update(UpdatePlatsRequest $request, Plats $plats)
    {
        $this->authorize('update', $plats);
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

    #[OA\Delete(
        path: "/api/plats/{id}",
        summary: "Delete plat",
        security: [["bearerAuth" => []]],
        tags: ["Plats"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Plat deleted"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            ),
            new OA\Response(
                response: 403,
                description: "Unauthorized"
            ),
            new OA\Response(
                response: 404,
                description: "Plat not found"
            )
        ]
    )]
    public function destroy(Plats $plats)
    {
        $this->authorize('delete', $plats);
        $plats->delete();
        return ["message" => "plats deleted"];
    }
}
