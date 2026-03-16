<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
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