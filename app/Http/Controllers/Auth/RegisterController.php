<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterController extends Controller
{

    public function showRegister(){
        return view("register");
    }

    public function register(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users,email",
            "phone" => "required|string|max:20",
            "company_name" => "required|string|max:255",
            "company_url" => "required|url",
            "password" => "required|min:6"
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "company_name" => $request->company_name,
            "url" => $request->company_url,
            "token" => Str::random(30),
            "password" => $request->password,
        ]);

        // return response()->json([
        //     "message" => "User registered successfully",
        //     "user" => $user
        // ], 201);

        return redirect()->route('login')->with("message","User Registerd Successfully");
    }

}
