<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\ProfileResource;


class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function loginProfile(LoginRequest $request)
    {
        $answer = $this->authService->login($request->input('name'), $request->input('password'));
        return response()->json(['status' => 'success',
        'message' => 'Successful login to profile',
        'data' => new ProfileResource($answer['profile']), 
        'token' => $answer['token']], 200);     
    }
    
    public function registerProfile(RegisterRequest $request)
    {
        $answer = $this->authService->register($request->input('name'), $request->input('password'));
        return response()->json(['status' => 'success',
        'message' => 'Successful profile creation',
        'data' => new ProfileResource($answer['profile']), 
        'token' => $answer['token']], 200);
    }
}
