<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

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


    #[OA\Get(
        path: '/api/products/list/{page}',
        summary: 'Get paginated list of products',
        tags: ['Products']
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'path',
        required: true,
        description: 'The page number to retrieve',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Product Retrieved Successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Product Retrieved Successfully.'),
                new OA\Property(property: 'totalrecords', type: 'integer', example: 50),
                new OA\Property(property: 'page', type: 'integer', example: 1),
                new OA\Property(property: 'totpage', type: 'integer', example: 10),
                new OA\Property(
                    property: 'products',
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Product')
                )
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Products is empty')]
    #[OA\Response(response: 500, description: 'Server Error')]
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

    #[OA\Get(
        path: '/api/products/search/{key}',
        summary: 'Search products by description',
        tags: ['Products']
    )]
    #[OA\Parameter(
        name: 'key',
        in: 'path',
        required: true,
        description: 'Search keyword for descriptions',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 200,
        description: 'Searched found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Searched found..'),
                new OA\Property(
                    property: 'products',
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Product')
                )
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Product not found')]
    #[OA\Response(response: 500, description: 'Server Error')]   
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
