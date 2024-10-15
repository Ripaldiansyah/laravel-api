<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tr_D_Sale;
use App\Models\Tr_H_Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $company_id = CompanyController::getCompanyId();
            $query = Tr_H_Sale::where(
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

            $sales = $query->paginate($limit);

            return response()->json([
                'data' => $sales
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
                'total_amount' => 'required|numeric',
                'total_quantity' => 'required|integer',
                'products' => 'required'
            ]);

            $company_id = CompanyController::getCompanyId();
            DB::beginTransaction();

            $tr_header = [
                'company_id' => $company_id,
                'total_amount' => $request->total_amount,
                'total_quantity' => $request->total_quantity
            ];

            $tr_details = $request->products;
            $header = Tr_H_Sale::create($tr_header);

            $products = [];
            $products_failed = [];

            foreach ($tr_details as $tr_detail) {
                $product = Product::where('id', $tr_detail["product_id"])->first();
                $product_detail = [
                    'tr_h_sales' => $header->id,
                    'product_name' => $product->product_name,
                    'price' => $product->price,
                    'quantity' => $tr_detail["quantity"],
                    'total' => $product->price * $tr_detail["quantity"]
                ];
                $stock = $product->stock - $tr_detail["quantity"];
                if ($stock >= 0) {
                    $product->update(['stock' => $stock]);
                    Tr_D_Sale::create($product_detail);
                    $products[] = $product_detail;
                } else {
                    $products_failed[] = $product_detail;
                    $header->update([
                        'total_amount' => ($header->total_amount - $product_detail["total"]),
                        'total_quantity' => ($header->total_quantity - $product_detail["quantity"])
                    ]);
                }
            }

            $response = [
                'id' =>  $header->id,
                'company_id' => $header->company_id,
                'total_amount' => $header->total_amount,
                'total_quantity' => $header->total_quantity,
                'created_at' => $header->created_at,
                'updated_at' => $header->updated_at,
                'products' => $products,
                'products_failed' => $products_failed
            ];

            DB::commit();
            return response()->json(
                $response,
                201
            );
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        try {
            $company_id = CompanyController::getCompanyId();
            $sale = Tr_H_Sale::where('id', $id)
                ->where('company_id', $company_id)->first();
            if (!$sale) {
                return response()->json([
                    'message' => "sale not found"
                ], 404);
            }

            $response = [
                'id' => $sale->id,
                'total_amount' => $sale->total_amount,
                'total_quantity' => $sale->total_quantity,
                'created_at' => $sale->created_at,
                'updated_at' => $sale->updated_at,
                'products' => $sale->details
            ];

            return response()->json([
                'data' => $response
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}