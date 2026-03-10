<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name'      => 'required',
            'email'     => 'required|email|unique:user',
            'password'  => 'required|confirmed',
        ]);

        $user = User::create($fields);
        $token = $user->createToken($request->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }


    public function login(Request $request)
    {
        $fields = $request->validate([
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $$user->password)) {

            return [
                'message' => 'Invalid Credentials'
            ];
        }

        $token = $user->createToken($request->email);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    
}


