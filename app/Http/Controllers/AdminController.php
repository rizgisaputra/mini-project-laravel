<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = User::all();
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'addres' => 'required',
            'no_hp' => 'required',
            'role' => 'required'
        ]);

        $result = User::create($validated);
        if($result == false){
            return response()->json([
                'message' => 'error add user'
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'data create sucessfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = User::where('id', $id)->first();
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
