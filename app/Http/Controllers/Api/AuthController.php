<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    // Register
    public function register(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'phone'    => 'required|digits:10|unique:users,phone',
                'password' => 'required|min:6',
                'role'     => 'required|in:customer,tailor,admin'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // 🔒 Admin direct register block
            // if ($request->role === 'admin') {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Admin cannot be registered from API'
            //     ], 403);
            // }

            // 👤 Create User
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'role'     => $request->role, // 👈 important
                'status'   => 1,
                'password' => Hash::make($request->password)
            ]);

            // 📄 Profile create (optional role-based logic)
            UserProfile::create([
                'user_id' => $user->id
            ]);

            // 👉 Tailor extra logic (future ready)
            if ($request->role === 'tailor') {
                // yaha tailor_profile table bana sakti ho
                // TailorProfile::create([...]);
            }

            // 🔑 Token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status'  => true,
                'message' => ucfirst($request->role) . ' registered successfully',
                'token'   => $token,
                'user'    => $user
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $th->getMessage()
            ], 500);
        }
    }


    // Login
    public function login(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'username'    => 'required', // email ya phone
                'password' => 'required',
                'role'     => 'required|in:customer,tailor,admin'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // 🔍 Check user (email OR phone)
            $user = User::where('email', $request->username)
                ->orWhere('phone', $request->username)
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // 🔒 Role check
            if ($user->role !== $request->role) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized role access'
                ], 403);
            }

            // 🔒 Status check
            if ($user->status == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Account inactive. Contact admin'
                ], 403);
            }

            // 🔑 Token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    // Logout
    public function logout(Request $request)
    {

        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }


    // Profile
    public function profile(Request $request)
    {

        $user = $request->user()->load('profile', 'addresses');

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }


    // Update Profile
    public function updateProfile(Request $request)
    {

        $user = $request->user();

        $user->update([
            'name' => $request->name ?? $user->name
        ]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $request->phone,
                'gender' => $request->gender,
                'dob' => $request->dob
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Profile updated'
        ]);
    }


    // Change Password
    public function changePassword(Request $request)
    {

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {

            return response()->json([
                'status' => false,
                'message' => 'Old password incorrect'
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password changed'
        ]);
    }


    // Forgot Password
    public function forgotPassword(Request $request)
    {

        $user = User::where('email', $request->email)->first();

        if (!$user) {

            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Reset link sent (implement email later)'
        ]);
    }


    // Reset Password
    public function resetPassword(Request $request)
    {

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password reset successful'
        ]);
    }
}
