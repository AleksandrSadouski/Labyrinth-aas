<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Http\Resources\ProfileResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;


class AuthController extends Controller
{
    public function loginProfile(LoginRequest $request)
    {
        $name = $request->input('name');
        $password = $request->input('password');
        $profile = Profile::with('player')->where('name', $name)->first();
        if (!$profile || !Hash::check($password, $profile->password))
            {
                return response()->json(['status' => 'error', 
                'message' => 'Incorrect data'], 401);
            }
        $profile->tokens()->delete();
        $token = $profile->createToken('auth-token')->plainTextToken;
        return response()->json(['status' => 'success',
        'message' => 'Successful login to profile',
        'data' => new ProfileResource($profile), 
        'token' => $token], 200);     
    }
    
    public function registerProfile(RegisterRequest $request)
    {
        $name = $request->input('name');
        $password = Hash::make($request->input('password'));
        $profile = new Profile();
        $profile->name = $name;
        $profile->password = $password;
        $profile->save();
        $token = $profile->createToken('auth-token')->plainTextToken;
        return response()->json(['status' => 'success',
        'message' => 'Successful profile creation',
        'data' => new ProfileResource($profile), 
        'token' => $token], 200);
    }
}
