<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Plats;
use App\Models\Recommendations;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function stats(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $mostRecommended = Plats::withAvg('recommendations', 'score')
            ->first(['id', 'name']);

        $leastRecommended = Plats::withAvg('recommendations', 'score')
            ->first(['id', 'name']);

        $topCategory = Category::withCount('plats')
            ->orderByDesc('plats_count')
            ->first(['id', 'name']);

        return response()->json([
            'total_plates'               => Plats::count(),
            'total_categories'           => Category::count(),
            'total_ingredients'          => Ingredient::count(),
            'total_recommendations'      => Recommendations::count(),
            'most_recommended_plate'     => $mostRecommended,
            'least_recommended_plate'    => $leastRecommended,
            'category_with_most_plates'  => $topCategory,
        ]);
    }
}
