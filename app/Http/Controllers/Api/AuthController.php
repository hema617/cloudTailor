<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    // Register User
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6',
            'phone'=>'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message'=>$validator->errors()
            ]);
        }

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);

        UserProfile::create([
            'user_id'=>$user->id,
            'phone'=>$request->phone
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'=>true,
            'message'=>'User Registered',
            'token'=>$token,
            'user'=>$user
        ]);

    }


    // Login
    public function login(Request $request)
    {

        $user = User::where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password,$user->password)){

            return response()->json([
                'status'=>false,
                'message'=>'Invalid Credentials'
            ]);

        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'=>true,
            'token'=>$token,
            'user'=>$user
        ]);

    }


    // Logout
    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'=>true,
            'message'=>'Logged out'
        ]);

    }


    // User Profile
    public function profile(Request $request)
    {

        $user = User::with('profile','addresses')->find($request->user()->id);

        return response()->json([
            'status'=>true,
            'data'=>$user
        ]);

    }


    // Update Profile
    public function updateProfile(Request $request)
    {

        $user = $request->user();

        $user->update([
            'name'=>$request->name
        ]);

        $user->profile->update([
            'phone'=>$request->phone
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Profile Updated'
        ]);

    }


    // Change Password
    public function changePassword(Request $request)
    {

        $user = $request->user();

        if(!Hash::check($request->old_password,$user->password)){
            return response()->json([
                'status'=>false,
                'message'=>'Old password incorrect'
            ]);
        }

        $user->update([
            'password'=>Hash::make($request->new_password)
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Password Updated'
        ]);

    }

}