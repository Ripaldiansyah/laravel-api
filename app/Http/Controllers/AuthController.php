<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        /** @var User $user **/
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


    public function logout(Request $request){
        $token = $request->bearerToken();
        if ($token){
            $user = Auth::user();
            $user->tokens()->where('token', hash('sha256', $token))->delete();
            return response()->json([
                'message' => 'Succesfully logget out'
            ]);
        }

        return  response()->json([
            'message' => 'Token is required'
        ]);
    }

}
