<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $fields = $request->validate([
            'name' => 'required|string|unique:categories,name',
            'description'  => 'nullable|string'
        ]);

        // dd($request->user());

        $category = Category::create($fields);
        $fields['user_id'] = auth()->id();
        return $category;
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $category;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $fields = $request->validate([
            'name' => 'required|string|unique:categories,name',
            'description'  => 'nullable|string'
        ]);

        $category->update($fields);
        return $category;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return ["message" => "category deleted"];
    }
}
