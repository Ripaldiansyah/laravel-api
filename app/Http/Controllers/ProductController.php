<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $company_id = CompanyController::getCompanyId();
            $query = Product::where(
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

            $products = $query->paginate($limit);

            return response()->json([
                'data' => $products
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
                'product_name' => 'required|string|max:50',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'sku' => 'required',
                'category_id' => 'required|integer'
            ]);

            $company_id = CompanyController::getCompanyId();
            $productRegistered = Product::where('product_name', $request->product_name)->where('company_id', $company_id)->first();
            if ($productRegistered) {
                $stock = $productRegistered->stock;
                $stock += $request->stock;
                $request["stock"] = $stock;
                return $this->update($request, $productRegistered->id);
            }
            $request->merge([
                'company_id' => $company_id,
            ]);
            $product = Product::create($request->all());
            return response()->json(
                $product,
                201
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
            $product = Product::where('id', $id)
                ->where('company_id', $company_id)->first();
            if (!$product) {
                return response()->json([
                    'message' => "product not found"
                ], 404);
            }
            $product->update($request->all());
            return response()->json([
                "data" => $product
            ], 200);
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
            $product = Product::where('id', $id)
                ->where('company_id', $company_id)->first();
            if (!$product) {
                return response()->json([
                    'message' => "product not found"
                ], 404);
            }

            $product->delete();
            return response()->json(['message' => 'product successfully deleted'], 200);
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
            $product = Product::where('id', $id)
                ->where('company_id', $company_id)->first();
            if (!$product) {
                return response()->json([
                    'message' => "product not found"
                ], 404);
            }

            return response()->json([
                'product' => $product
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}