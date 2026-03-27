<?php

namespace App\Http\Controllers;

use App\Jobs\AnalyzeRecommendation;
use App\Models\Plats;
use App\Models\Recommendations;
use App\Http\Requests\UpdateRecommendationsRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use OpenApi\Attributes as OA;

class RecommendationsController extends Controller
{
    use AuthorizesRequests;

    #[OA\Get(
        path: "/api/recommendations",
        summary: "Get all recommendations",
        security: [["bearerAuth" => []]],
        tags: ["Recommendations"],
        responses: [
            new OA\Response(response: 200, description: "List of recommendations with plat and user"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Unauthorized")
        ]
    )]
    public function index()
    {
        $this->authorize('viewAny', Recommendations::class);

        return response()->json(
            Recommendations::with('plat', 'user')->get()
        );
    }

    #[OA\Post(
        path: "/api/recommendations/{plat_id}",
        summary: "Request a recommendation analysis for a plat",
        security: [["bearerAuth" => []]],
        tags: ["Recommendations"],
        parameters: [
            new OA\Parameter(name: "plat_id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 202, description: "Analysis started"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Unauthorized"),
            new OA\Response(response: 404, description: "Plat not found")
        ]
    )]
    public function store($plat_id)
    {
        $this->authorize('create', Recommendations::class);

        $plat = Plats::findOrFail((int) $plat_id);

        $recommendation = Recommendations::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'plat_id' => $plat->id,
            ],
            [
                'score' => 0,
                'warning_message' => null,
                'status' => 'pending',
            ]
        );
        AnalyzeRecommendation::dispatch($recommendation);
        return response()->json([
            'message' => 'Analysis started',
            'status' => 'pending',
            'data' => $recommendation->load('plat', 'user'),
        ], 202);
    }

    #[OA\Get(
        path: "/api/recommendations/{plat_id}",
        summary: "Get recommendation result for a plat",
        security: [["bearerAuth" => []]],
        tags: ["Recommendations"],
        parameters: [
            new OA\Parameter(name: "plat_id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Recommendation score and status"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Unauthorized"),
            new OA\Response(response: 404, description: "Recommendation not found")
        ]
    )]
    public function show($plat_id)
    {
        $recommendation = Recommendations::with('plat')
            ->where('user_id', auth()->id())
            ->where('plat_id', (int) $plat_id)
            ->latest()
            ->firstOrFail();

        $this->authorize('view', $recommendation);

        return response()->json([
            'plat_id' => $recommendation->plat_id,
            'score' => $recommendation->score,
            'status' => $recommendation->status,
            'warning_message' => $recommendation->warning_message,
        ]);
    }
}
