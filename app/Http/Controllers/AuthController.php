<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token-name', ['server:update'])->plainTextToken;
            //           $user->tokens()->where('token', hash('sha256', $token))->update([
            //              'created_at' => now(),
            //              'update_at' => now()->addMinutes(5)
            //           ]);
            return response()->json([
                'data' => [
                    "id" => $user->id,
                    "email" => $user->email,
                    "role" => $user->role,
                    "token" => $token,

                ]
            ]);
        }

        return  response()->json([
            "message" => "Wrong email or password"
        ]);
    }


    public function logout(Request $request)
    {
        $fullToken = $request->bearerToken();

        if ($fullToken) {
            $user = Auth::user();

            $tokenParts = explode('|', $fullToken);
            $token = end($tokenParts);

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
