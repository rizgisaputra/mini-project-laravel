<?php

namespace App\Http\Controllers;

use App\Models\HeaderCustomerCart;
use Illuminate\Http\Request;

class HeaderCustomerCartController extends Controller
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
        }

        if($role == 'admin'){
            $data = HeaderCustomerCart::all();
        }else if($role == 'customer'){
            $data = HeaderCustomerCart::select('user_id', 'total_product', 'total_quantity', 'total_price')
            ->where('user_id', $user_id)->get();
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
        }

        if($role == 'admin'){
            $data = HeaderCustomerCart::select('user_id', 'total_product', 'total_quantity', 'total_price')
            ->where('id', $id)->get();
        }else if($role == 'customer'){
            $data = HeaderCustomerCart::select('user_id', 'total_product', 'total_quantity', 'total_price')
            ->where('user_id', $user_id)
            ->where('id', $id)->get();
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
}
