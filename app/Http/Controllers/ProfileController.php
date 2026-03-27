<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    #[OA\Get(
        path: "/api/profile",
        summary: "Get authenticated user profile",
        security: [["bearerAuth" => []]],
        tags: ["Profile"],
        responses: [
            new OA\Response(response: 200, description: "User profile details"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function index()
    {
        $user = auth()->user();

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'dietary_tags' => $user->dietary_tags ?? []
        ]);
    }

    public function create()
    {
        //
    }

    public function store()
    {
        //
    }

    #[OA\Put(
        path: "/api/profile/{profile}",
        summary: "Update authenticated user profile",
        security: [["bearerAuth" => []]],
        tags: ["Profile"],
        parameters: [
            new OA\Parameter(name: "profile", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", example: "john@example.com"),
                    new OA\Property(
                        property: "dietary_tags",
                        type: "array",
                        items: new OA\Items(type: "string", enum: ["vegan", "vegetarian", "halal", "kosher", "gluten_free", "dairy_free"]),
                        example: ["vegan", "halal"]
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Profile updated successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function update(Request $request)
    {
        $user = auth()->user();

        $fields = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'dietary_tags' => 'nullable|array',
            'dietary_tags.*' => ['string', Rule::in(User::DIETARY_TAGS)],
        ]);

        $user->update($fields);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'dietary_tags' => $user->dietary_tags ?? []
            ]
        ]);
    }
}
