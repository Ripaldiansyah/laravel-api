<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index(Request $request)
    {

        try {

            $current_user = Auth::user();

            $query = User::where(
                'company_id',
                $current_user->company_id
            );
            if ($request->has('sort_field') && $request->has('sort_type')) {
                $sortField = $request->sort_field;
                $sortDirection = $request->sort_type;

                $query->orderBy($sortField, $sortDirection);
            }

            $limit = $request->has('limit') ? (int)$request->limit : 10;
            if ($limit > 50) {
                $limit = 50;
            }
            $users = $query->paginate($limit);

            return response()->json([
                'data' => $users
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);


            $current_user = Auth::user();
            $company_id = $current_user->company_id;

            $request->merge([
                'company_id' => $company_id,
                'role' => 'user',
                'status' => 'Active',
                'photo' => 1,
            ]);
            $user = User::create($request->all());
            return response()->json($user, 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }


    public function show($id)
    {
        $current_user = Auth::user();
        $company_id = $current_user->company_id;


        $user = User::where('company_id', $company_id)
            ->where('id', $id)->first();

        if (!$user) {
            return response()->json([
                "error" => "user not found"
            ]);
        }

        return response()->json([
            "user" => $user
        ]);
    }

    public function update(Request $request, $id)
    {

        $current_user = Auth::user();
        $company_id = $current_user->company_id;


        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'status' => 'required|string',
        ]);

        if($validator->fails()){
            return  response()->json([
               'error' => $validator->errors()
            ], 400);
        }

        $user = User::findOrFail($id);



        if ($user->company_id !=$company_id ){
            return response()->json([
                "error" => "user not found"
            ], 404);
        }




        $data = $request->all();
        $data["company_id"] = $company_id;
        $user->update($data);

        return response()->json([
            "user" => $user
        ], 200);
    }

    // Remove the specified resource from storage.
    public function destroy($id)
    {

        $user = User::findOrFail($id);
        $current_user = Auth::user();
        $company_id = $current_user->company_id;



        if ($user->company_id !=$company_id ){
            return response()->json([
                "error" => "user not found"
            ], 404);
        }

        $user->delete();
        return response()->json([
            "message" => "successfully deleted user"
        ], 200);
    }
}
