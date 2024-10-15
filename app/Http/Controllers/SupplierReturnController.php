<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tr_D_Purchase;
use App\Models\Tr_D_SupplierReturn;
use App\Models\Tr_H_Purchase;
use App\Models\Tr_H_SupplierReturn;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierReturnController extends Controller
{
    public function index(Request $request)
    {
        try {
            $company_id = CompanyController::getCompanyId();
            $query = Tr_H_Purchase::where('company_id', $company_id);

            if ($request->has('sort_field') && $request->has('sort_type')) {
                $sortField = $request->sort_field;
                $sortDirection = $request->sort_type;

                $query->orderBy($sortField, $sortDirection);
            }
            $limit = $request->has('limit') ? (int)$request->limit : 10;
            if ($limit > 50) {
                $limit = 50;
            }

            $returns = $query->paginate($limit);

            return response()->json([
                'data' => $returns
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
                'tr_h_purchase' => 'required|integer',
                'total_quantity' => 'required|integer',
                'total_amount' => 'required|numeric',
                'products' => 'required'
            ]);


            $purchase = Tr_H_Purchase::where('id', $request->tr_h_purchase)
                ->where('status', 'release')->first();

            if (!$purchase) {
                return response()->json([
                    'message' => "Purchase not found"
                ], 404);
            }


            $company_id = CompanyController::getCompanyId();
            DB::beginTransaction();
            $tr_header = [
                'company_id' => $company_id,
                'tr_h_purchase' => $request->tr_h_purchase,
                'total_amount' => $request->total_amount,
                'total_quantity' => $request->total_quantity,
                'reason' => $request->reason,
                'status' => "pending",
            ];


            $header = Tr_H_SupplierReturn::create($tr_header);
            $products = [];


            $tr_details = $request->products;
            foreach ($tr_details as $tr_detail) {
                $product_purchase = Tr_D_Purchase::where('tr_h_purchase', $header->tr_h_purchase)->where('product_id', $tr_detail["product_id"])->first();
                $product_detail = [
                    'tr_h_supplier_returns' => $header->id,
                    'product_id' =>  $tr_detail["product_id"],
                    'return_price' => $product_purchase->purchase_price,
                    'return_quantity' => $tr_detail["return_quantity"],
                    'return_amount' => $tr_detail["return_quantity"] *  $product_purchase->purchase_price,
                ];
                Tr_D_SupplierReturn::create($product_detail);
                $products[] = $product_detail;
            }

            $response = [
                'id' =>  $header->id,
                'company_id' => $header->company_id,
                'total_amount' => $header->total_amount,
                'total_quantity' => $header->total_quantity,
                'created_at' => $header->created_at,
                'updated_at' => $header->updated_at,
                'products' => $products,
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

    public function update(Request $request, $id)
    {
        try {
            $company_id = CompanyController::getCompanyId();
            $header = Tr_H_SupplierReturn::where('id', $id)
                ->where('company_id', $company_id)
                ->where('status', 'pending')->first();
            if (!$header) {
                return response()->json([
                    'message' => "return not found"
                ], 404);
            }

            DB::beginTransaction();

            $tr_details = $request->products;
            $total_quantity = 0;
            $total_amount = 0;
            $products = [];
            foreach ($tr_details as $tr_detail) {
                $detail = Tr_D_SupplierReturn::where('tr_h_supplier_returns', $header->id)->where('product_id', $tr_detail["product_id"])->first();
                $product = Product::find($tr_detail["product_id"]);
                $total_quantity_temp =  $tr_detail["return_quantity_approve"];
                $total_amount_temp =  $tr_detail["return_quantity_approve"] * $detail["return_price"];
                $stock = $product->stock - $tr_detail["return_quantity_approve"];

                $product->update([
                    "stock" => $stock
                ]);
                $detail->update([
                    'return_quantity_approve' => $total_quantity_temp,
                    'return_amount_approve' => $total_amount_temp
                ]);

                $total_quantity += $total_quantity_temp;
                $total_amount += $total_amount_temp;
                $products[] = $detail;
            }

            $header->update([
                "total_quantity" => $total_quantity,
                "total_amount" => $total_amount,
                "status" => "approve"
            ]);

            $response = [
                'id' =>  $header->id,
                'company_id' => $header->company_id,
                'total_amount' => $header->total_amount,
                'total_quantity' => $header->total_quantity,
                'created_at' => $header->created_at,
                'updated_at' => $header->updated_at,
                'products' => $products,
            ];

            DB::commit();
            return response()->json([
                "data" => $response
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $company_id = CompanyController::getCompanyId();
            $header = Tr_H_SupplierReturn::where('id', $id)
                ->where('company_id', $company_id)
                ->where('status', 'pending')
                ->first();
            if (!$header) {
                return response()->json([
                    'message' => "return not found"
                ], 404);
            }

            Tr_D_SupplierReturn::where('tr_h_supplier_returns', $header->id)->delete();
            $header->delete();
            return response()->json(['message' => 'Return successfully deleted'], 200);
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
            $return = Tr_H_SupplierReturn::where('id', $id)
                ->where('company_id', $company_id)->first();
            if (!$return) {
                return response()->json([
                    'message' => "return not found"
                ], 404);
            }


            $response = [
                'id' => $return->id,
                'supplier_id' => $return->purchase->supplier_id,
                'total_amount' => $return->total_amount,
                'total_quantity' => $return->total_quantity,
                'created_at' => $return->created_at,
                'updated_at' => $return->updated_at,
                'products' => $return->details,
                'purchase' => $return->purchase
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
