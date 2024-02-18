<?php

namespace App\Http\Controllers;

use App\Models\HeaderDetailOrder;
use App\Models\HeaderOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function showHeaderForAdmin(){
        $role = auth()->user()->role;
        if($role == 'seller'){
            return response()->json([
                'message' => 'seller cannot look history'
            ], 403);
        }

        $data = HeaderOrder::all();
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }


    public function showHeaderDetailForAdmin(string $id){
        $role = auth()->user()->role;
        if($role == 'seller'){
            return response()->json([
                'message' => 'seller cannot look history'
            ], 403);
        }

        $data = HeaderDetailOrder::find($id);
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

    public function showHeaderForCustomer(){
        $role = auth()->user()->role;
        $user_id = auth()->id();
        if($role == 'seller'){
            return response()->json([
                'message' => 'seller cannot look history'
            ], 403);
        }

        $data = HeaderOrder::all()->where('user_id', $user_id);
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }

    public function showHeaderDetailForCustomer(string $id){
        $role = auth()->user()->role;
        $user_id = auth()->id();
        if($role == 'seller'){
            return response()->json([
                'message' => 'seller cannot look history'
            ], 403);
        }

        $data = DB::table('header_details_orders AS hd')
        ->select('hd.id','hd.header_order_id', 'hd.product_id',
        'hd.quantity', 'hd.price', 'hd.created_at', 'hd.updated_at')
        ->join('header_orders AS h', 'hd.header_order_id', '=', 'h.id')
        ->where('h.user_id', $user_id)
        ->where('hd.id', $id)->first();

        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        return response()->json([
            'status'=> 'ok',
            'data' => $data
        ], 200);
    }

    public function showHistoryForSeller(){
        $user_id = auth()->id();
        $data = DB::table('header_details_orders AS hd')
        ->join('products AS p', 'hd.product_id', '=', 'p.id')
        ->select('p.name', 'hd.quantity', 'hd.price', 'hd.created_at', 'hd.updated_at')
        ->where('p.user_id', $user_id)->get();

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
