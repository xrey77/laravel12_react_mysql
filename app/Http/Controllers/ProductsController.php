<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    public function addProduct(Request $request) {
        $product = Product::where('descriptions', $request->descriptions)->first();
        if ($product) {
            return response()->json(['message' => 'Product Description is already exists!'],404);
        }
        $product->category = $request->category;
        $product->descriptions = $request->descriptions;
        $product->qty = $request->qty;
        $product->unit = $request->unit;
        $product->costprice = $request->costprice;
        $product->sellprice = $request->sellprice;
        $product->saleprice = $request->saleprice;
        $product->productpicture = $request->productpicture;
        $product->alertstocks = $request->alertstocks;
        $product->criticalstocks = $request->criticalstocks;
        $product->save();
        return response()->json(['message' => 'New Product Created Successfully.'],200);
    }

    public function listProducts(Request $request, int $page) 
    {
        $perPage = 5;
        $skip = ($page - 1) * $perPage;
        try {
            $products = Product::skip($skip)->take($perPage)->get();
            $totalrecords = Product::count(); 
            $totpage = ceil($totalrecords / $perPage);


            if ($products->count() == 0) {
                return response()->json(['message' => 'Products is empty.'],404);
            }
            return response()->json(['message' => 'Product Retrieved Successfully.', 'totalrecords' => $totalrecords, 'page' => $page,'totpage'=> $totpage, 'products' => $products],200);
        } catch(\Exceptions $e) {
            return response()->json(['message' => $e->getMessage()],500);
        }
    }

    public function productSearch(string $key) {
        try {
            $products = Product::where('descriptions', 'LIKE', '%' . $key . '%')->get();
            if ($products->count() == 0) {
                return response()->json(['message' => 'Product not found.'],404);
            }
            return response()->json(['message' => 'Searched found..', 'products' => $products],200);
        } catch(\Exceptions $e) {
            return response()->json(['message' => $e->getMessage()],500);
        }

    }
}
