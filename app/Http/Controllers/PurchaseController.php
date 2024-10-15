<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tr_D_Purchase;
use App\Models\Tr_H_Purchase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        try {
            $company_id = CompanyController::getCompanyId();
            $query = Tr_H_Purchase::where(
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

            $purchases = $query->paginate($limit);

            return response()->json([
                'data' => $purchases
            ], 200);
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
            $purchase = Tr_H_Purchase::where('id', $id)
                ->where('company_id', $company_id)->first();
            if (!$purchase) {
                return response()->json([
                    'message' => "purchase not found"
                ], 404);
            }

            $response = [
                'id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'total_amount' => $purchase->total_amount,
                'total_quantity' => $purchase->total_quantity,
                'created_at' => $purchase->created_at,
                'updated_at' => $purchase->updated_at,
                'products' => $purchase->details
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


    public function store(Request $request)
    {
        try {
            $request->validate([
                'supplier_id' => 'required|integer',
                'total_quantity' => 'required|integer',
                'total_amount' => 'required|numeric',
                'products' => 'required'

            ]);

            $company_id = CompanyController::getCompanyId();
            DB::beginTransaction();



            $tr_header = [
                'company_id' => $company_id,
                'supplier_id' => $request->supplier_id,
                'total_amount' => $request->total_amount,
                'total_quantity' => $request->total_quantity,
                'status' => "Unrelease",
            ];


            $header = Tr_H_Purchase::create($tr_header);
            $products = [];


            $tr_details = $request->products;
            foreach ($tr_details as $tr_detail) {

                $product_detail = [
                    'tr_h_purchase' => $header->id,
                    'product_id' =>  $tr_detail["product_id"],
                    'purchase_price' => $tr_detail["purchase_price"],
                    'purchase_quantity' => $tr_detail["purchase_quantity"],
                    'purchase_amount' => $tr_detail["purchase_quantity"] * $tr_detail["purchase_price"],
                    'exp_date' => $tr_detail["exp_date"],
                ];
                Tr_D_Purchase::create($product_detail);
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
            $header = Tr_H_Purchase::where('id', $id)
                ->where('company_id', $company_id)
                ->where('status', 'unrelease')->first();
            if (!$header) {
                return response()->json([
                    'message' => "product not found"
                ], 404);
            }

            DB::beginTransaction();

            $tr_details = $request->products;
            $total_quantity = 0;
            $total_amount = 0;
            $products = [];
            foreach ($tr_details as $tr_detail) {
                $detail = Tr_D_Purchase::where('tr_h_purchase', $header->id)->where('product_id', $tr_detail["product_id"])->first();
                $product = Product::find($tr_detail["product_id"]);
                $total_quantity_temp =  $tr_detail["purchase_quantity_release"];
                $total_amount_temp =  $tr_detail["purchase_quantity_release"] * $detail["purchase_price"];
                $stock = $product->stock + $tr_detail["purchase_quantity_release"];
                $product->update([
                    "stock" => $stock
                ]);
                $detail->update([
                    'purchase_quantity_release' => $total_quantity_temp,
                    'purchase_amount_release' => $total_amount_temp
                ]);

                $total_quantity += $total_quantity_temp;
                $total_amount += $total_amount_temp;
                $products[] = $detail;
            }

            $header->update([
                "total_quantity" => $total_quantity,
                "total_amount" => $total_amount,
                "status" => "release"
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
            $header = Tr_H_Purchase::where('id', $id)
                ->where('company_id', $company_id)
                ->where('status', 'unrelease')
                ->first();
            if (!$header) {
                return response()->json([
                    'message' => "Purchase not found"
                ], 404);
            }

            Tr_D_Purchase::where('tr_h_purchase', $header->id)->delete();
            $header->delete();
            return response()->json(['message' => 'Purchase successfully deleted'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}
