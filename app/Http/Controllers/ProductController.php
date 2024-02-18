<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user_id = auth()->id();
        $searchProduct = $request->query("product");
        $filterByCategory = $request->query("category");

        if(isset($searchProduct)){
            $data = Product::where("name","ILIKE","%".$searchProduct."%")
            ->orWhere("name","ILIKE".$searchProduct."%")
            ->orWhere("name","ILIKE","%".$searchProduct)
            ->where('user_id', $user_id)->get();

            return response()->json([
                'status' => 'ok',
                'data' => $data
            ], 200);
        }

        if(isset($filterByCategory)){
            $data = DB::table('products AS p')->select('p.id', 'p.name', 'p.img',
            'p.description', 'p.price', 'p.stock', 'p.user_id')
            ->join('products_categories AS pc', 'p.id', '=', 'pc.product_id')
            ->where('pc.category_id', $filterByCategory)
            ->where('p.user_id', $user_id)->get();

            return response()->json([
                'status' => 'ok',
                'data' => $data
            ]);
        }

        $data = Product::select('id','name', 'img', 'price')->where('user_id', $user_id)->get();
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }

    public function indexForAdminAndCustomer(Request $request){
        $searchProduct = $request->query("product");
        $filterByCategory = $request->query("category");

        if(isset($searchProduct)){
            $data = Product::where("name","ILIKE","%".$searchProduct."%")
            ->orWhere("name","ILIKE".$searchProduct."%")
            ->orWhere("name","ILIKE","%".$searchProduct)->get();

            return response()->json([
                'status' => 'ok',
                'data' => $data
            ], 200);
        }

        if(isset($filterByCategory)){
            $data = DB::table('products AS p')->select('p.id', 'p.name', 'p.img',
            'p.description', 'p.price', 'p.stock', 'p.user_id')
            ->join('products_categories AS pc', 'p.id', '=', 'pc.product_id')
            ->where('pc.category_id', $filterByCategory)->get();

            return response()->json([
                'status' => 'ok',
                'data' => $data
            ]);
        }

        $data = Product::select('id','name', 'img', 'price')->get();
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $role = auth()->user()->role;
        $validated = 0;
        if($role == 'customer'){
            return response()->json([
                'message' => 'customer cannot add product'
            ], 403);
        }else if($role == 'seller'){
            $validated = $request->validate([
                'name' => 'required',
                'img' => 'required',
                'description' => 'required',
                'price' => 'required',
                'stock' => 'required',
                'categories' => 'array|required',
                'categories.*.id' => 'required'
            ]);

            $user_id = auth()->id();
            $validated['user_id'] = $user_id;
        }

        if($validated['stock'] == 0){
            return response()->json([
                'message' => 'stock cannot empty'
            ], 400);
        }

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

    public function storeForAdmin(Request $request){
        $validated = $request->validate([
            'name' => 'required',
            'img' => 'required',
            'description' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'user_id' => 'required',
            'categories' => 'array|required',
            'categories.*.id' => 'required'
        ]);

        $getID = User::where('id', $validated['user_id'])->where('role', 'seller')->first();
        if($getID == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        if($validated['stock'] == 0){
            return response()->json([
                'message' => 'stock cannot empty'
            ], 400);
        }

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

    public function showForAdminAndCustomer(string $id){
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
    }

    public function update(Request $request, string $id)
    {
        $role = auth()->user()->role;
        if($role == 'customer'){
            return response()->json([
                'message' => 'customer cannot update data'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'nullable',
            'img' => 'nullable',
            'description' => 'nullable',
            'price' => 'nullable',
            'stock' => 'nullable',
            'categories' => 'nullable|array',
            'categories.*.id' => 'required'
        ]);

        if(isset($validated['stock'])){
            if($validated['stock'] == 0){
                return response()->json([
                    'message' => 'stock cannot is empty'
                ], 400);
            }
        }

        $user_id = auth()->id();
        $data = Product::where('id', $id)->where('user_id', $user_id)->first();
        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        if(isset($validated['categories'])){
            $data_product_category = ProductCategory::where('product_id', $id)->first();
            if($data_product_category == null){
                return response()->json([
                    'message' => 'data product category not found'
                ], 404);
            }

            $data_product_category->delete();
            $data_product = Product::where('id', $id)->where('user_id', $user_id)->first();
            if($data_product == null){
                return response()->json([
                    'message' => 'data not found'
                ], 404);
            }

            $categories = $validated['categories'];
            unset($validated['categories']);

            $result = $data_product->update($validated);
            if($result == false){
                return response()->json([
                    'message' => 'error update data'
                ], 500);
            }

            DB::beginTransaction();
            try{
                foreach($categories as $category){
                    $dt = [
                        'product_id' => $id,
                        'category_id' => $category['id']
                    ];
                    ProductCategory::create($dt);
                }
                DB::commit();
                return response()->json([
                    'message' => 'update sucessfully'
                ], 200);

            }catch(\Throwable $th){
                DB::rollBack();
                return response()->json([
                    'message' => 'data failed to create'
                ], 500);
            }
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

    public function updateForAdmin(Request $request, string $id){
        $validated = $request->validate([
            'name' => 'required',
            'img' => 'required',
            'description' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'categories' => 'array|required',
            'categories.*.id' => 'required'
        ]);

        if($validated['stock'] == 0){
            return response()->json([
                'message' => 'stock cannot is empty'
            ], 400);
        }

        $data = Product::where('id', $id)->first();
        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        if(isset($validated['categories'])){
            $data_product_category = ProductCategory::where('product_id', $id)->first();
            if($data_product_category == null){
                return response()->json([
                    'message' => 'data product category not found'
                ], 404);
            }

            $data_product_category->delete();
            $data_product = Product::where('id', $id)->first();
            if($data_product == null){
                return response()->json([
                    'message' => 'data not found'
                ], 404);
            }

            $categories = $validated['categories'];
            unset($validated['categories']);

            $result = $data_product->update($validated);
            if($result == false){
                return response()->json([
                    'message' => 'error update data'
                ], 500);
            }

            DB::beginTransaction();
            try{
                foreach($categories as $category){
                    $dt = [
                        'product_id' => $id,
                        'category_id' => $category['id']
                    ];
                    ProductCategory::create($dt);
                }
                DB::commit();
                return response()->json([
                    'message' => 'update sucessfully'
                ], 200);

            }catch(\Throwable $th){
                DB::rollBack();
                return response()->json([
                    'message' => 'data failed to create'
                ], 500);
            }
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
        $role = auth()->user()->role;

        if($role == 'customer'){
            return response()->json([
                'message' => 'customer cannot delete data'
            ], 403);
        }

        $user_id = auth()->id();
        $data_product_category = ProductCategory::where('product_id', $id)->first();
        if($data_product_category == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $user_id = auth()->id();
        $data = Product::where('id', $id)->where('user_id', $user_id)->first();
        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $delete_data_product_category = $data_product_category->delete();
        if($delete_data_product_category == false){
            return response()->json([
                'message' => 'error to delete data product category'
            ], 500);
        }

        $result = $data->delete();
        if($result == false){
            return response()->json([
                'message' => 'error delete data'
            ], 500);
        }

        return response()->noContent();
    }

    public function destroyForAdmin(string $id){
        $data_product_category = ProductCategory::where('product_id', $id)->first();
        if($data_product_category == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $data = Product::where('id', $id)->first();
        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $delete_data_product_category = $data_product_category->delete();
        if($delete_data_product_category == false){
            return response()->json([
                'message' => 'error to delete data product category'
            ], 500);
        }

        $result = $data->delete();
        if($result == false){
            return response()->json([
                'message' => 'error delete data'
            ], 500);
        }

        return response()->noContent();
    }

    public function updateCategoryInProductCategories(Request $request, string $id){
        $data = Product::where('id', $id)->first();
        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $validated = $request->validate([
            'category_id' => 'required'
        ]);

        $data_product_category = ProductCategory::select('category_id')->where('product_id', $id)->get();
        if($data_product_category == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $result = $data->update($validated);
        if($result == false){
            return response()->json([
                'message' => 'failed to update data'
            ], 500);
        }

        return response()->json([
            'message' => 'update data sucessfully'
        ], 200);
    }
}
