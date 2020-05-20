<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{

    /* 
    | Register a new user via the API 
    */
    public function register(Request $request) 
    {
        $validatedData = $request->validate([
            'name'=>'required',
            'email'=>'email|required|unique:users',
            'password'=>'required|confirmed'
        ]);

        // encrypt the password
        $validatedData['password'] = bcrypt($request->password);

        // create a new user and save it in the DB
        $user = User::create($validatedData);

        // create and return the access token and the user object
        $accessToken = $user->createToken('authToken')->accessToken;
        return response(['user'=>$user, 'access_token'=>$accessToken]);

    }


    /* 
    | Log in existing user via the API 
    */
    public function login(Request $request) 
    {
        $validatedData = $request->validate([
            'email'=>'email|required',
            'password'=>'required'
        ]);

        // we attempt to log in this user with the validated credentials
        if (!auth()->attempt($validatedData)) {
            return response(['message'=>__('auth.failed')], 402);
        }

        // get the user object and return it together with a new access token
        $user = auth()->user();
        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['user'=>$user, 'access_token'=>$accessToken]);

    }

}
