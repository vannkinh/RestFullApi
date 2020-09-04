<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Exceptions\JWTException;

class AuthController extends Controller
{
    public $loginAfterSignUp = true;


    // login
    public function login(Request $request){
        $credentials = $request->only("email", "password");
        $token = null;

        if(!$token = JWTAuth::attempt($credentials))
        {
            return response() -> json([
                "status" => false,
                "message" => "unauthorized"
            ]);
        }
        return response()->json([
            "status" => true,
            "token" => $token
        ]);
    }


    //Register
    public function register(Request $request){
        $this->validate($request, [
            "name" => "required|string",
            "email" => "required|email|unique:users",
            "password" => "required|string|min:6|max:10"
        ]);

        $user = new User();
        $user -> name = $request->name;
        $user -> email = $request->email;
        $user -> password = bcrypt ($request->password);
        $user -> save();

        if($this->loginAfterSignUp){
            return $this -> login($request);
        }
        return response()-> json([
            "status" => true,
            "user" => $user
        ]);
    }


    //logout
    public function logout (Request $request)
    {
        $this->validate($request, [
            "token" => "required"
        ]);
        
        try {
            JWTAuth::invalidate($request -> token);
            return response() -> json([
                "status" => true,
                "message" => "user logged out successfully"
            ]);
        }catch (JWTException $exception){
            return response() -> json([
                "status" => false,
                "message" => "Ops, the user can't logged out"
            ]);
        }
    }
}
