<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        // Validate new user
        $data = $request->only(['name', 'email', 'password']);
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|max:20'
        ]);

        if ($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }
    }
}
