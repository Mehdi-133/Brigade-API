<?php

namespace App\Http\Controllers;

use App\Models\Recommendations;
use App\Http\Requests\UpdateRecommendationsRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Models\Plats;

class RecommendationsController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Recommendations::class);
        return Recommendations::with('plat', 'user')->get();
    }

    public function store(Request $request, $plat_id)
    {
        $this->authorize('create', Recommendations::class);

        $plat = Plats::with('ingredients')->findOrFail($plat_id);

        $recommendation = Recommendations::create([
            'plat_id'         => $plat->id,
            'user_id'         => auth()->id(),
            'score'           => 0,
            'warning_message' => 'Pending AI analysis',
            'status'          => 'pending',
        ]);

        return response()->json($recommendation->load('plat', 'user'), 201);
    }

    public function show(Recommendations $recommendations)
    {
        $this->authorize('view', $recommendations);
        return $recommendations->load('plat', 'user');
    }

    public function update(UpdateRecommendationsRequest $request, Recommendations $recommendations)
    {
        $this->authorize('update', $recommendations);
        $recommendations->update($request->validated());
        return response()->json($recommendations);
    }

    public function destroy(Recommendations $recommendations)
    {
        $this->authorize('delete', $recommendations);
        $recommendations->delete();
        return response()->json(null, 204);
    }
}
