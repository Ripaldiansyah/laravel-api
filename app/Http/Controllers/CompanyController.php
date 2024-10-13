<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        try {
            $companies = Company::paginate(10);
            return response()->json(['data' => $companies], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'company_name' => 'required|string|max:255|unique:companies',
                'description' => 'nullable|string',
                'photo' => 'nullable|string',
                'address' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'working_hour_start' => 'nullable|date_format:H:i',
                'working_hour_end' => 'nullable|date_format:H:i',
                'status' => 'nullable|string',
            ]);

            $company = Company::create($request->all());
            return response()->json($company, 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $company = Company::findOrFail($id);
            return response()->json(['company' => $company], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Company not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $company = Company::findOrFail($id);

            $request->validate([
                'company_name' => 'required|string|max:255|unique:companies,company_name,' . $company->id,
                'description' => 'nullable|string',
                'photo' => 'nullable|string',
                'address' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'working_hour_start' => 'nullable|date_format:H:i',
                'working_hour_end' => 'nullable|date_format:H:i',
                'status' => 'nullable|string',
            ]);

            $company->update($request->all());
            return response()->json(['company' => $company], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            $company->delete();
            return response()->json(['message' => 'Company successfully deleted'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Company not found'], 404);
        }
    }
}

