<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;

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

        // Invalid request response
        if ($validator->fails()){
            return response()->json(['error' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Valid request, create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        // User created
        return response()->json([
            'error' => false,
            'message' => 'User created successfully',
            $data
        ], Response::HTTP_OK);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        // Validator
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:8|max:20'
        ]);
        
        // Invalid request response
        if ($validator->fails()){
            return response()->json(['error' => true, $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Validated request
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                	'error' => true,
                	'message' => 'Invalid login credentials',
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (JWTException $e) {
    	    return $credentials;
            return response()->json([
                	'error' => true,
                	'message' => 'Could not create token.',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
 	
 		//Token created, return with success response and jwt token
        return response()->json([
            'error' => false,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        // Validator
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

		//Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                'error' => false,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'error' => true,
                'message' => 'User cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        $user = JWTAuth::authenticate($request->token);
 
        return response()->json(['user' => $user]);
    }
}
