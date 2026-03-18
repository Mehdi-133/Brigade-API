<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Plats;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    #[OA\Get(
        path: "/api/category",
        summary: "Get all categories",
        security: [["bearerAuth" => []]],
        tags: ["Categories"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of categories"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            )
        ]
    )]
    public function index()
    {
        return Category::all();
    }

    #[OA\Post(
        path: "/api/category",
        summary: "Create new category",
        security: [["bearerAuth" => []]],
        tags: ["Categories"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Desserts"),
                    new OA\Property(property: "description", type: "string", example: "Sweet dishes"),
                    new OA\Property(property: "color", type: "string", example: "#0000FF"),
                    new OA\Property(property: "is_active", type: "boolean", example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Category created successfully"
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
    public function store(Request $request)
    {
        $this->authorize('create',  Category::class);

        $fields = $request->validate([
            'name' => 'required|string|unique:categories,name|max:255',
            'description' => 'nullable|string',
            'color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'is_active' => 'boolean'
        ]);

        $category = $request->user()->category()->create($fields);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);
    }

    #[OA\Get(
        path: "/api/category/{id}",
        summary: "Get category by ID",
        security: [["bearerAuth" => []]],
        tags: ["Categories"],
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
                description: "Category details"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            ),
            new OA\Response(
                response: 404,
                description: "Category not found"
            )
        ]
    )]
    public function show(Category $category)
    {
        return $category;
    }

    #[OA\Put(
        path: "/api/category/{id}",
        summary: "Update category",
        security: [["bearerAuth" => []]],
        tags: ["Categories"],
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
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "description", type: "string"),
                    new OA\Property(property: "color", type: "string", example: "#0000FF"),
                    new OA\Property(property: "is_active", type: "boolean", example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Category updated successfully"
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
                description: "Category not found"
            )
        ]
    )]
    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);
        $fields = $request->validate([
            'name' => 'required|string|unique:categories,name',
            'description'  => 'nullable|string',
            'color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'is_active' => 'boolean'
        ]);

        $category->update($fields);
        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ], 200);
    }

    #[OA\Delete(
        path: "/api/category/{id}",
        summary: "Delete category",
        security: [["bearerAuth" => []]],
        tags: ["Categories"],
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
                description: "Category deleted"
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
                description: "Category not found"
            )
        ]
    )]
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();
        return ["message" => "category deleted"];
    }
}
