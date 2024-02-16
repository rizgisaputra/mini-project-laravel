<?php

namespace App\Http\Controllers;

use App\Models\CustomerCart;
use App\Models\HeaderCustomerCart;
use App\Models\HeaderDetailCustomerCart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CustomerCartController extends Controller
{
    public function index()
    {
        $user_id = auth()->id();
        $data = CustomerCart::where('user_id', $user_id)->get();
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }

    public function indexForAdmin(Request $request){
        $data = CustomerCart::all();
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'quantity' => 'required'
        ]);

        $getStockProduct = Product::select('stock')->where('id', $validated['product_id'])->first();
        if($validated['quantity' > $getStockProduct]){
            return response()->json([
                'message' => 'quantity higher than stock'
            ], 500);
        }

        $findProduct = Product::where('id', $validated['product_id'])->first();
        if($findProduct == null){
            return response()->json([
                'message' => 'data user or product not found'
            ], 404);
        }

        $price = Product::select('price')->where('id', $validated['product_id'])->sum('price');
        if($price == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $getAll = CustomerCart::all();
        foreach($getAll as $data){
            if($data['product_id'] == $validated['product_id'] && $data['user_id'] == auth()->id()){
                $validated['quantity'] += $data['quantity'];
                $validated['total'] = $price * $validated['quantity'];

                $data = CustomerCart::where('product_id', $validated['product_id'])
                ->where('user_id', auth()->id())->first();

                $result = $data->update($validated);
                if($result == false){
                    return response()->json([
                        'message' => 'error to create data'
                    ], 500);
                }

                return response()->json([
                    'message' => 'data create sucessfully'
                ],201);
            }
        }

        $validated['user_id'] = auth()->id();
        $validated['total'] = $price * $validated['quantity'];
        $result = CustomerCart::create($validated);
        if($result == false){
            return response()->json([
                'message' => 'failed to create data'
            ], 500);
        }

        return response()->json([
            'message' => 'data create sucessfully'
        ],201);
    }

    public function storeForAdmin(Request $request){
        $validated = $request->validate([
            'user_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required'
        ]);

        $getStockProduct = Product::select('stock')->where('id', $validated['product_id'])->sum('stock');
        if($validated['quantity'] > $getStockProduct){
            return response()->json([
                'message' => 'quantity higher than stock'
            ], 500);
        }

        $findUser = User::where('id', $validated['user_id'])->where('role', 'customer')->first();
        $findProduct = Product::where('id', $validated['product_id'])->first();
        if($findUser == null || $findProduct == null){
            return response()->json([
                'message' => 'data user or product not found'
            ], 404);
        }

        $price = Product::select('price')->where('id', $validated['product_id'])->sum('price');
        if($price == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $getAll = CustomerCart::all();
        foreach($getAll as $data){
            if($data['product_id'] == $validated['product_id'] && $data['user_id'] == $validated['user_id']){
                $validated['quantity'] += $data['quantity'];
                $validated['total'] = $price * $validated['quantity'];

                $data = CustomerCart::where('product_id', $validated['product_id'])
                ->where('user_id', $validated['user_id'])->first();

                $result = $data->update($validated);
                if($result == false){
                    return response()->json([
                        'message' => 'error to create data'
                    ], 500);
                }

                return response()->json([
                    'message' => 'data create sucessfully'
                ],201);
            }
        }

        $validated['total'] = $price * $validated['quantity'];
        $result = CustomerCart::create($validated);
        if($result == false){
            return response()->json([
                'message' => 'failed to create data'
            ], 500);
        }

        return response()->json([
            'message' => 'data create sucessfully'
        ],201);
    }

    public function show(string $id)
    {
        $data = CustomerCart::where('id', $id)->where('user_id', auth()->id())->first();
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

    public function showForAdmin(Request $request, string $id){
        $data = CustomerCart::where('id', $id)->first();
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

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'quantity' => 'required'
        ]);

        $findProduct= Product::where('id', $validated['product_id'])->first();
        if($findProduct == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $validated['total'] = $findProduct['price'] * $validated['quantity'];
        $data = CustomerCart::where('id', $id)->where('user_id', auth()->id());
        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $result = $data->update($validated);
        if($result == false){
            return response()->json([
                'message' => 'error to update data'
            ], 500);
        }

        return response()->json([
            'message' => 'update data sucessfully'
        ], 200);
    }

    public function updateForAdmin(Request $request, string $id){
        $validated = $request->validate([
            'product_id' => 'required',
            'quantity' => 'required'
        ]);

        $findProduct= Product::where('id', $validated['product_id'])->first();
        if($findProduct == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $validated['total'] = $findProduct['price'] * $validated['quantity'];
        $data = CustomerCart::where('id', $id);
        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $result = $data->update($validated);
        if($result == false){
            return response()->json([
                'message' => 'error to update data'
            ], 500);
        }

        return response()->json([
            'message' => 'update data sucessfully'
        ], 200);
    }

    public function destroy(string $id)
    {
        $data = CustomerCart::where('id', $id)->where('user_id', auth()->id())->first();
        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $result = $data->delete();
        if($result == false){
            return response()->json([
                'message' => 'error to delete data'
            ], 500);
        }
        return response()->noContent();
    }

    public function destroyForAdmin(Request $request, string $id){
        $data = CustomerCart::where('id', $id)->first();
        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        $result = $data->delete();
        if($result == false){
            return response()->json([
                'message' => 'error to delete data'
            ], 500);
        }
        return response()->noContent();
    }

    public function checkoutFromCart(Request $request){
        if(auth()->user()->role == 'admin' || auth()->user()->role == 'seller'){
            return response()->json([
                'message' => 'customer or admin cannot checkout'
            ], 403);
        }

        $validated = $request->validate([
            'carts' => 'array||required',
            'carts.*.id' => 'required'
        ]);

        $carts = $validated['carts'];
        foreach($carts as $cart){
            $data = CustomerCart::where('id', $cart['id'])->where('user_id', auth()->id())->first();
            if($data == null){
                return response()->json([
                    'message' => 'data not found'
                ]);
            }

            $data_product = CustomerCart::select('product_id')
            ->where('id', $cart['id'])->where('user_id', auth()->id())->first();
            $id_product = $data_product['product_id'];

            $quantity = CustomerCart::select('quantity')
            ->where('id', $cart['id'])->where('user_id', auth()->id())->sum('quantity');

            $product = Product::where('id', $id_product)->first();
            $stock = Product::select('stock')->where('id', $id_product)->sum('stock');
            $stock -= $quantity;
            $product->stock = $stock;
            $product->save();

            $result = $data->delete();
            if($result == false){
                return response()->json([
                    'error failed update data'
                ], 500);
            }
        }
    }

    public function checkout(Request $request){
        $validated = $request->validate([
           'product_id' => 'required',
           'quantity' => 'required'
        ]);
        
    }
}
