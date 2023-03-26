<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function login()
    {
        try {
            $requestData = request()->input();
            $user = User::where("login", $requestData["login"])->first();
            Log::channel("stack")->info("Request register new user data", [
                "requestData" => $requestData, "user" => $user
            ]);
            if ($user == null) {
                Log::channel("stack")->error("error occurred while logging in user ");
                return response()->json([
                    "status" => "not ok"
                ], 400);
            } elseif ($user->password == sha1($requestData["password"])) {
                $res = response()->json([
                    "status" => "ok",
                    "id" => $user->id,
                    "name" => $user->id,
                    "surname" => $user->surname,
                    "login" => $user->login,
                ]);
                Log::channel("stack")->info("response register new user data", [
                    "response" => $res
                ]);
                return $res;

            } else {
                Log::channel("stack")->error("e111rror occurred while logging in user ", [$user->password === Hash::make($requestData["password"])]);
                return response()->json([
                    "status" => "not ok"
                ], 400);
            }
        } catch (\Throwable $t){
            Log::channel("stack")->error("Exception occurred while logging in user ", [
                "code" => $t->getCode(),
                "message" => $t->getMessage(),
                "line" => $t->getLine(),
                "file" => $t->getFile(),
            ]);
            return response()->json([
                "status" => "not ok"
            ], 400);
        }

    }

    public function register()
    {
        $requestData = request()->input();

        try {
            $user = User::create([
                "name" => $requestData["name"],
                "surname" => $requestData["surname"],
                "login" => $requestData["login"],
                "password" => sha1($requestData["password"]),
            ]);
            Log::channel("stack")->info("Request register new user data", [
                "requestData" => $requestData, "user" => $user
            ]);
        } catch (Exception $t) {
            Log::channel("stack")->error("Error occurred while creating new User", [
                "code" => $t->getCode(),
                "message" => $t->getMessage(),
                "line" => $t->getLine(),
                "file" => $t->getFile(),
            ]);
            return response()->json([
                "status" => "not ok"
            ], 400);
        }
        return response()->json([
            "status" => "ok",
            "id" => $user->id,
            "name" => $user->name,
            "surname" => $user->surname,
            "login" => $user->login,
        ], 201);
    }
}
