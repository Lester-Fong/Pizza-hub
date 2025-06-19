<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Config;
use Log;

class UserService
{
    protected $user_model;

    public function __construct()
    {
        $this->user_model = new User();
    }

    public function registerUser($data)
    {
        $messages = Config::get('Constants.MESSAGES');

        $this->user_model->create([
            'name' => $data['firstname'] . ' ' . $data['lastname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // ====== can send email verification here if needed ======= //

        return response()->json([
            'success' => true,
            'message' =>  $messages['SUCCESS_REGISTER'],
        ], 201);
    }

    public function loginUser($data)
    {
        $messages = Config::get('Constants.MESSAGES');

        $user = $this->user_model->where("email", $data["email"])->first();
        if (!isset($user) || !Hash::check($data["password"], $user->password)) {
            return response()->json(["error" => true, "message" => "Invalid credentials"], 401);
        }

        $token = $user->createToken($user->id . " - user")->plainTextToken;
        return response()->json([
            'success' => true,
            'message' => $messages['SUCCESS_LOGIN'],
            'token' => $token,
        ], 200);
    }


    public function logoutUser()
    {
        $messages = Config::get('Constants.MESSAGES');
        $user = auth()->user();
        Log::debug(print_r($user, true));
        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                'success' => true,
                'message' => $messages['SUCCESS_LOGOUT'],
            ], 200);
        }

        return response()->json(['error' => true, 'message' => 'User not authenticated'], 401);
    }
}
