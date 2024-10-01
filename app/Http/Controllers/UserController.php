<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        return User::all();
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'status' => 'required|string',
            // tambahkan validasi lain sesuai kebutuhan
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
            'company_id' => $request->company_id, // jika ada
            // tambahkan field lain sesuai kebutuhan
        ]);

        return response()->json($user, 201);
    }

    // Display the specified resource.
    public function show(User $user)
    {
        return $user;
    }

    // Update the specified resource in storage.
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:6',
            'role' => 'sometimes|required|string',
            'status' => 'sometimes|required|string',
            // tambahkan validasi lain sesuai kebutuhan
        ]);

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->fill($request->only(['name', 'email', 'role', 'status']));
        $user->save();

        return response()->json($user);
    }

    // Remove the specified resource from storage.
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}
