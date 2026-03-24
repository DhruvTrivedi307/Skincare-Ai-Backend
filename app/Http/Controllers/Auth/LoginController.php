<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        if (!Auth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ])) {
            // return response()->json([
            //     "error" => "Invalid credentials"
            // ], 401);
            return redirect()->route("login")->with("error","Invalid email or password.");
        }

        $user = Auth::user();

        $check_user = User::where('email', $request->email)->first();

        if($check_user->role === "Super-Admin"){
            return redirect()->route("dashboard")->with("message","Logged in successfully");
        } else {
            return redirect()->route("dashboard")->with("message","Logged in successfully");
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('message', 'Logged out successfully');
    }
}
