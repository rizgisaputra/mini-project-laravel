<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        if(auth()->user()->role == 'customer' || auth()->user()->role == 'admin'){
            $data = Product::select('name', 'img', 'price')->get();
            return response()->json([
                'status' => 'ok',
                'data' => $data
            ], 200);
        }else if(auth()->user()->role == 'seller'){
            $user_id = auth()->id();
            $data = Product::select('name', 'img', 'price')->where('user_id', $user_id)->get();
            return response()->json([
                'status' => 'ok',
                'data' => $data
            ], 200);
        }
    }

    public function store(Request $request)
    {
        if(auth()->user()->role == 'customer' || auth()->user()->role == 'admin'){
            return response()->json([
                'message' => 'customer or admin cannot add product'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required',
            'img' => 'required',
            'description' => 'required',
            'price' => 'required',
            'categories' => 'array|required',
            'categories.*.id' => 'required'
        ]);

        $user_id = auth()->id();
        $validated['user_id'] = $user_id;
        $categories = $validated['categories'];
        unset($validated['categories']);

        DB::beginTransaction();
        try{
            $product = Product::create($validated);
            $createdCategories = [];

            foreach($categories as $category){
                $dt = [
                    'product_id' => $product->id,
                    'category_id' => $category['id']
                ];
                $createdCategories[] =  ProductCategory::create($dt);
            }
            $product->categories = $createdCategories;
            DB::commit();

        }catch(\Throwable $th){
            DB::rollBack();
            return response()->json([
                'message' => 'data failed to create'
            ], 500);
        }

        return response()->json([
            'message' => 'create data sucessfully'
        ], 201);
    }

    public function show(string $id)
    {
        if(auth()->user()->role == 'customer' || auth()->user()->role == 'admin'){
            $data = Product::where('id', $id)->first();
            if($data == null){
                return response()->json([
                    'message' => 'data not found'
                ], 404);
            }

            return response()->json([
                'message' => 'ok',
                'data' => $data
            ], 200);
        }else if(auth()->user()->role == 'seller'){
            $user_id = auth()->id();
            $data = Product::where('id', $id)->where('user_id', $user_id)->first();
            if($data == null){
                return response()->json([
                    'message' => 'data not found'
                ], 404);
            }

            return response()->json([
                'message' => 'ok',
                'data' => $data
            ], 200);
        }
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'nullable',
            'img' => 'nullable',
            'description' => 'nullable',
            'price' => 'nullable'
        ]);

        if(auth()->user()->role == 'customer' || auth()->user()->role == 'admin'){
            return response()->json([
                'message' => 'customer or admin cannot update data'
            ], 403);
        }

        $user_id = auth()->id();
        $data = Product::where('id', $id)->where('user_id', $user_id)->first();
        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $result = $data->update($validated);
        if($result == false){
            return response()->json([
                'message' => 'error update data'
            ], 500);
        }

        return response()->json([
            'message' => 'data update sucesfully'
        ], 200);
    }

    public function destroy(string $id)
    {
        if(auth()->user()->role == 'customer'){
            return response()->json([
                'message' => 'customer cannot delete data'
            ], 403);
        }

        $user_id = auth()->id();
        $data = Product::where('id', $id)->where('user_id', $user_id)->first();
         if($data == false){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $result = $data->delete();
        if($result == false){
            return response()->json([
                'message' => 'error delete data'
            ], 500);
        }

        return response()->noContent();
    }
}
