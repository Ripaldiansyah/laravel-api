<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;


class AuthController extends Controller
{

    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token-name', ['server:update'])->plainTextToken;
            $tokenParts = explode('|', $token);
            $token = end($tokenParts);

            Log::info('Logout request received', [
                'user' => $user,
                
            ]);

            return response()->json([
                'data' => [
                    "id" => $user->id,
                    "email" => $user->email,
                    "role" => $user->role,
                    "token" => $token,

                ]
            ]);
        }

        return response()->json([
            "message" => "Wrong email or password"
        ], 401);

    }


    public function logout(Request $request)
    {


        $token = $request->bearerToken();

        if ($token) {
            $user = Auth::user();
            $currentUserToken = $user->tokens()->where('token', hash('sha256', $token))->first();

            if (!$currentUserToken) {
                return response()->json([
                    'message' => 'Invalid token'
                ], 401);
            }


            $currentUserToken->delete();

            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        }

        return response()->json([
            'message' => 'Token is required'
        ], 400);
    }
}
