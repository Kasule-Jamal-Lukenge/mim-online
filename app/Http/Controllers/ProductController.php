<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //Listing all products
    public function index(){
        return response()->json(Product::with('category')->get());
    }

    //Adding A New Product
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|string'
        ]);

        $product = Product::create($request->all());

        return response()->json([
            'message' => 'Product created successfully.',
            'product' => $product
        ], 201);
    }

    //Show One Product
    public function show($id){
        $product = Product::with('category')->find($id);

        if(! $product){
            return response()->json(['message' => 'Product Not Found.'], 404);
        }

        return response()->json($product);
    }

    //Update Product
    public function update(Request $request, $id){
        $product = Product::find($id);

        if(! $product){
            return response()->json(['message' => 'Product Not Found.'], 400);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:products,name' .$id,
            'price' => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'description' => 'nullable|string'
        ]);

        $product->update($request->all());

        return response()->json([
            'message' => 'Product Updated Successfully.',
            'product' => $product
        ], 200);
    }

    //Deleting A Product
    public function destroy($id){
        $product = Product::find($id);

        if(! $product){
            return response()->json(['message' => 'Product Not Found'], 404);
        }

        $product->delete();

        return response()->json(['message' => "Product Deleted Successfully."]);
    }

}
