<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $company_id = CompanyController::getCompanyId();
            $query = Category::where(
                'company_id',
                $company_id
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

            $categories = $query->paginate($limit);

            return response()->json([
                'data' => $categories
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
                'category_name' => 'required|string|max:50',
            ]);

            $company_id = CompanyController::getCompanyId();

            $categoryRegistered = Category::where('category_name', $request->category_name)->
            where('company_id', $company_id)->first();


            if ($categoryRegistered) {
                throw new Exception('Category already registered.');
            }

            $request->merge([
                'company_id' => $company_id,

            ]);

            $category = Category::create($request->all());
            return response()->json(
                $category, 201
            );


        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $company_id = CompanyController::getCompanyId();
            $category = Category::where('id', $id)
                ->where('company_id', $company_id)->first();

            if (!$category) {
                return response()->json([
                    'message' => "category not found"
                ], 404);
            }

            $category->update($request->all());
            return response()->json($category, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $company_id = CompanyController::getCompanyId();
            $category = Category::where('id', $id)
                ->where('company_id', $company_id)->first();
            if (!$category) {
                return response()->json([
                    'message' => "category not found"
                ], 404);
            }

            $category->delete();
            return response()->json(['message' => 'category successfully deleted'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        try {
            $company_id = CompanyController::getCompanyId();
            $category = Category::where('id', $id)
                ->where('company_id', $company_id)->first();
            if (!$category) {
                return response()->json([
                    'message' => "$category not found"
                ], 404);
            }

            $response = [
                'id' => $category->id,
                'category_name' => $category->category_name,
                'company_id' => $category->company_id,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
                'products' => $category->products
            ];

            return response()->json([
                'category' => $response
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }

    }
}
