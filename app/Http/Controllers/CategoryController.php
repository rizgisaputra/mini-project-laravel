<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchCategory = $request->query("category");
        if(isset($searchCategory)){
            $data = Category::where("category","ILIKE","%".$searchCategory."%")
            ->orWhere("category","ILIKE".$searchCategory."%")
            ->orWhere("category","ILIKE","%".$searchCategory)->get();

            return response()->json([
                'status' => 'ok',
                'data' => $data
            ], 200);
        }

        $data = Category::select('category')->get();
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(auth()->user()->role == 'customer'){
            return response()->json([
              'message'=> 'customer cannot add category'
            ], 403);
        }

        $validate = $request->validate([
            'category' => 'required',
            'description' => 'required'
        ]);

        $result = Category::create($request->all());
        if($result == false){
            return response()->json([
                'message' => 'failed to add category'
            ], 500);
        }

        return response()->json([
            'message' => 'category create sucessfully'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Category::find($id);
        if($data == false){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        if(auth()->user()->role == 'customer'){
            return response()->json([
              'message'=> 'customer cannot update category'
            ], 403);
        }

        $validate = $request->validate([
            'category' => 'nullable',
            'description' => 'nullable'
        ]);

        $data = Category::find($id);
        $result = $data->update($validate);
        if($result == false){
            return response()->json([
                'message' => 'failed to update category'
            ], 500);
        }

        return response()->json([
            'message' => 'category update sucessfully'
        ], 200);
    }

    public function destroy(string $id)
    {
        if(auth()->user()->role == 'customer'){
            return response()->json([
              'message'=> 'customer cannot delete category'
            ], 403);
        }

        $data = Category::find($id);
        if($data == false){
            return response()->json([
                'messsage' => 'data not found'
            ], 404);
        }

        $result = $data->delete();
        if($result == false){
            return response()->json([
                'message' => 'failed to delete data'
            ], 500);
        }

        return response()->noContent();
    }
}
