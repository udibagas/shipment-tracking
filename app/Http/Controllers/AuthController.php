<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $auth = Auth::attempt($request->only(['email', 'password']));

        if ($auth) {
            $user = User::find(auth()->id());
            $user->token = $user->createToken($request->device_name ?: 'web')->plainTextToken;
            return $user;
        }

        return response(['message' => 'Wrong username or password'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response('', 204);
    }

    public function me()
    {
        return auth()->user();
    }
}
