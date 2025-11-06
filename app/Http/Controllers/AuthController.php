<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //Registers
    public function register(Request $request){
        try{
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|unique:users,phone',
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)->mixedCase()->numbers()->symbols(),
                ]
            ]);

            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'buyer',
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
        }catch (\Illuminate\Validation\ValidationException $e) {
            // Laravel automatically gives the messages array
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error'   => $e->getMessage(),
            ], 500);
        }
        
    }

    //Login with either the phone number or email
    public function login(Request $request){
        try{
            $request->validate([
                'identifier' => 'required|string',
                'password' => 'required|string',
            ]);

            //Identifying whether it is an email or a phone number
            $user = User::where('email', $request->identifier)
                        ->orWhere('phone', $request->identifier)
                        ->first();

            // if(! $user || !Hash::check($request->password, $user->password)){
            //     return response()->json(['message' => 'Invalid Credentials.'], 401);
            // }

            if(! $user){
                return response()->json([
                    'message' => "No account found with this email or phone number.",
                ], 404);
            }

            if(! Hash::check($request->password, $user->password)){
                return response()->json([
                    'message' => 'Invalid password provided.',
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login Successful',
                'user' => $user,
                'token' => $token,
            ], 200);
        }catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //Logout a user and invalidate the current token
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out succcessfully.']);
    }

    //Retrieving the profile of a logged in user
    public function profile(Request $request){
        return response()->json($request->user());
    }
}
