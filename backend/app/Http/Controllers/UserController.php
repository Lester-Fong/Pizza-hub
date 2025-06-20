<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Log;
use Config;

class UserController extends Controller
{
    public function register(RegisterUserRequest $request, UserService $user_service)
    {
        try {
            return $user_service->registerUser($request->validated());
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json(["error" => true, "message" => "An error occurred"], 500);
        }
    }

    public function login(LoginUserRequest $request, UserService $user_service)
    {
        $data = $request->validated();

        try {
            return $user_service->loginUser($data);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'An error occurred'], 500);
        }
    }

    public function logout(UserService $user_service)
    {
        try {
            return $user_service->logoutUser();
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'An error occurred'], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            return auth()->user();
        } catch (\Exception $e) {
            Log::error('Profile retrieval error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'An error occurred'], 500);
        }
    }
}
