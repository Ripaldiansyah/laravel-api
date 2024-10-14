<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        try {
            $company_id = CompanyController::getCompanyId();
            $query = Supplier::where(
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

            $suppliers = $query->paginate($limit);

            return response()->json([
                'data' => $suppliers
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
            $supplier = Supplier::where('id', $id)
                ->where('company_id', $company_id)->first();
            if (!$supplier) {
                return response()->json([
                    'message' => "Supplier not found"
                ], 404);
            }

            return response()->json([
                'data' => $supplier
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
                'supplier_name' => 'required|string|max:50',
                'supplier_address' => 'required|string|max:255',
            ]);

            $company_id = CompanyController::getCompanyId();
            $supplierRegistered = Supplier::where('supplier_name', $request->supplier_name)->where('company_id', $company_id)->first();


            if ($supplierRegistered) {
                throw new Exception('Supplier already registered.');
            }
            $request->merge([
                'company_id' => $company_id,

            ]);

            $supplier = Supplier::create($request->all());
            return response()->json(
                $supplier,
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
            $supplier = Supplier::where('id', $id)
                ->where('company_id', $company_id)->first();

            if (!$supplier) {
                return response()->json([
                    'message' => "Supplier not found"
                ], 404);
            }

            $supplier->update($request->all());
            return response()->json($supplier, 200);
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
            $supplier = Supplier::where('id', $id)
                ->where('company_id', $company_id)->first();
            if (!$supplier) {
                return response()->json([
                    'message' => "supplier not found"
                ], 404);
            }

            $supplier->delete();
            return response()->json(['message' => 'supplier successfully deleted'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}