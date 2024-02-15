<?php

namespace App\Http\Controllers;

use App\Models\HeaderCustomerCart;
use App\Models\HeaderDetailCustomerCart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HeaderDetailCustomerCartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role = auth()->user()->role;
        $user_id = auth()->id();
        $data = 0;

        if($role == 'seller'){
            return response()->json([
                'message' => 'seller cannot look cart'
            ], 403);
        }else if($role == 'admin'){
            $data = HeaderDetailCustomerCart::all();
        }else if($role == 'customer'){
            $data = DB::table('headers_detail_customers_carts AS hd')
            ->select('customer_cart_id', 'product_id', 'quantity', 'sub_total')
            ->join('headers_customers_carts AS h', 'hd.customer_cart_id', '=', 'h.id')
            ->where('h.user_id', $user_id)->get();
        }

        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);

    }

    public function store(Request $request)
    {
        $user_id = auth()->id();
        $validated = $request->validate([
            'product_id' => 'required',
            'quantity' => 'required',
        ]);

        $get_customer_cart_id = HeaderCustomerCart::where('user_id', $user_id)->get();
        if($get_customer_cart_id == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $product = $validated['product_id'];
        $price = Product::select('price')->where('id', $product)->sum('price');
        if($price == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $quantity = $validated['quantity'];
        $validated['customer_cart_id'] = $get_customer_cart_id['id'];
        $validated['sub_total'] = $price * $quantity;

        $result = HeaderDetailCustomerCart::create($validated);
        if($result == false){
            return response()->json([
                'message' => 'failed to create data'
            ], 500);
        }

        return response()->json([
            'message' => 'data create sucessfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = auth()->user()->role;
        $user_id = auth()->id();
        $data = 0;

        if($role == 'seller'){
            return response()->json([
                'message' => 'seller cannot look cart'
            ], 403);
        }else if($role == 'admin'){
            $data = HeaderDetailCustomerCart::all()->where('id', $id)->first();
        }else if($role == 'customer'){
            $data = DB::table('headers_detail_customers_carts AS hd')
            ->select('customer_cart_id', 'product_id', 'quantity', 'sub_total')
            ->join('headers_customers_carts AS h', 'hd.customer_cart_id', '=', 'h.id')
            ->where('h.user_id', $user_id)
            ->where('hd.id', $id)->get();
        }

        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user_id = auth()->id();
        $role = auth()->user()->role;
        $get_customer_cart_id = 0;
        $data = 0;
        if($role == 'seller'){
            return response()->json([
                'message' => 'seller cannot update cart'
            ], 403);
        }

        $validated = $request->validate([
            'product_id' => 'nullable',
            'quantity' => 'nullable',
        ]);

        if($role == 'admin'){
            $get_customer_cart_id = HeaderCustomerCart::where('user_id', $user_id)->first();
            $data = HeaderDetailCustomerCart::where('id', $id)->frist();
        }else if($role == 'customer'){
            $get_customer_cart_id = HeaderCustomerCart::where('user_id', $user_id)->first();
            $data = DB::table('headers_detail_customers_carts AS hd')
            ->select('customer_cart_id', 'product_id', 'quantity', 'sub_total')
            ->join('headers_customers_carts AS h', 'hd.customer_cart_id', '=', 'h.id')
            ->where('h.user_id', $user_id)
            ->where('hd.id', $id)->get();
        }

        if($get_customer_cart_id == null || $data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $product = $validated['product_id'];
        $price = Product::select('price')->where('id', $product)->sum('price');
        if($price == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $quantity = $validated['quantity'];
        $validated['customer_cart_id'] = $get_customer_cart_id['id'];
        $validated['sub_total'] = $price * $quantity;

        $result = $data->update($validated);
        if($result == false){
            return response()->json([
                'message' => 'failed to update data'
            ], 500);
        }

        return response()->json([
            'message' => 'data update sucessfully'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = auth()->user()->role;
        $user_id = auth()->id();
        $data = 0;

        if($role == 'seller'){
            return response()->json([
                'message' => 'seller cannot delete cart'
            ], 403);
        }else if($role == 'admin'){
            $data = HeaderDetailCustomerCart::all()->where('id', $id)->first();
        }else if($role == 'customer'){
            $data = DB::table('headers_detail_customers_carts AS hd')
            ->select('customer_cart_id', 'product_id', 'quantity', 'sub_total')
            ->join('headers_customers_carts AS h', 'hd.customer_cart_id', '=', 'h.id')
            ->where('h.user_id', $user_id)
            ->where('hd.id', $id)->get();
        }

        if($data == null){
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
