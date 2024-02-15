<?php

namespace App\Http\Controllers;
use App\Models\HeaderCustomerCart;
use App\Models\HeaderDetailCustomerCart;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email'=> 'required|string|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email','password');
        $token = Auth::attempt($credentials);

        if(!$token){
            return response()->json([
                'status' => 'error',
                'message' => 'email/password wrong'
            ], 401);
        }

        $role = User::select('role')->where('email', $credentials['email'])->first();
        return response()->json([
            'status' => 'ok',
            'token' => $token,
            'role' => $role
        ]);
    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email'=> 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'addres' => 'required|string|max:255',
            'no_hp' => 'required|string|max:255'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'addres' => $request->addres,
            'no_hp' => $request->no_hp,
            'role' => 'customer'
        ];
        $user = User::create($data);

        $data_header_cart = [
            'user_id' => $user->id,
            'total_product' => null,
            'total_quantity' => null,
            'total_price' => null
        ];
        $header_cart = HeaderCustomerCart::create($data_header_cart);
        return response()->json([
           'status'=> 'registration sucessfully',
        ]);

    }

    public function addSeller(Request $request){
        if(auth()->user()->role == 'customer' || auth()->user()->role == 'seller'){
            return response()->json([
                'message' => 'customer or seller cannot add seller'
            ], 403);
        }

        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'email'=> 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'addres' => 'required|string|max:255',
            'no_hp' => 'required|string|max:255'
        ]);

        $validate['role'] = 'seller';
        $data = User::create($validate);
        if($data == false){
            return response()->json([
                'message' => 'failed to add seller'
            ], 500);
        }

        return response()->json([
            'message' => 'create new seller sucesfully'
        ], 201);
    }
}
