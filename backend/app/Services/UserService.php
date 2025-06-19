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
            'name' => $data->input('firstname') . ' ' . $data->input('lastname'),
            'email' => $data->input('email'),
            'password' => Hash::make($data->input('password')),
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
            'user' => $user,
        ], 200);
    }


    public function logoutUser($request)
    {
        $messages = Config::get('Constants.MESSAGES');
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
