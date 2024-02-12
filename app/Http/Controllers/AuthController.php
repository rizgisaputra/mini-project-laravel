<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

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
        return response()->json([
            'status' => 'ok',
            'token' => $token
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
        return response()->json([
           'status'=> 'registration sucessfully',
        ]);

    }

    public function logout(Request $request){
        Auth::logout();
        return response()->json([
            'status' => 'succes',
            'message' => 'sucesfully logout'
        ]);
    }
}
