<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    //Listing all Categories
    public function index(){
        return response()->json(Category::all);
    }

    //Adding A New Category (Only Admin)
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string'
        ]);

        $category = Category::create($request->all());

        return response()->json([
            'message' => 'Category created successfully.',
            'category' => $category
        ], 201);
    }

    //Showing A Single Category
    public function show($id){
        $category = Category::find($id);

        if(! $category){
            return response()->json(['message' => 'Category Not Found'], 404);
        }

        return response()->json($category);
    }

    //Updating A Category
    public function update(Request $request, $id){
        $category = Category::find($id);

        if(! $category){
            return response()->json(['message' => 'Category Not Found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:categories,name,' .$id,
            'description' => 'nullable|string'
        ]);

        $category->update($request->all());

        return response()->json([
            'message' => 'Category Updated Successfully.',
            'category' => $category
        ], 200);
    }

    //Deleting A Category
    public function destroy($id){
        $category = Category::find($id);

        if(! $category){
            return response()->json(['message' => 'Category Not Found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category Deleted Successfully.'], 200);
    }
}
