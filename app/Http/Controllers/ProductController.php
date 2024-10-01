<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index',compact( ['products']));
    }

    public function create()
    {

        return view('products.create');
    }

    public function store(Request $request)
    {
       $product = Product::create($request->all());
        return view('products.create');
    }



}
