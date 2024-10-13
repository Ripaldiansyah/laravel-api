<?php

namespace App\Http\Controllers;

use App\Models\UserRequestHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserRequestHistoryController extends Controller
{
    public function index(Request $request)
    {
        $histories = UserRequestHistory::paginate($request->get('limit', 10));
        return response()->json($histories, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'request_type' => 'required|string|max:255',
            'request_data' => 'nullable|json',
            'status' => 'required|string|in:pending,completed,failed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $history = UserRequestHistory::create($request->all());
        return response()->json($history, 201);
    }

    public function show($id)
    {
        $history = UserRequestHistory::find($id);

        if (!$history) {
            return response()->json(['error' => 'Request history not found'], 404);
        }

        return response()->json($history, 200);
    }

    public function update(Request $request, $id)
    {
        $history = UserRequestHistory::find($id);
        if (!$history) {
            return response()->json(['error' => 'Request history not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|required|exists:users,id',
            'request_type' => 'sometimes|required|string|max:255',
            'request_data' => 'nullable|json',
            'status' => 'sometimes|required|string|in:pending,completed,failed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $history->update($request->all());
        return response()->json($history, 200);
    }

    public function destroy($id)
    {
        $history = UserRequestHistory::find($id);
        if (!$history) {
            return response()->json(['error' => 'Request history not found'], 404);
        }

        $history->delete();
        return response()->json(['message' => 'Request history successfully deleted'], 200);
    }
}
