<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkinAnalysis;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function customers()
    {
        if(Auth::user()->role === "Super-Admin"){
            $users = User::paginate(10);
        } else {
            $admin_token = User::where('token', Auth::user()->token)->first();
            $users = SkinAnalysis::where('admin_id', $admin_token->id)->distinct('user_token')->paginate(10);
            return view("admin-users", compact("users"));
        }
        return view("users", compact("users"));
    }

    public function addCustomers(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users,email",
            "phone" => "required|string|max:20",
            "company_name" => "required|string|max:255",
            "url" => "required|url",
            "password" => "required|min:6"
        ]);

        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "company_name" => $request->company_name,
            "url" => $request->url,
            "token" => Str::random(30),
            "password" => $request->password,
        ]);

        return redirect()->route('customers')->with('success', 'Customer added successfully.');
    }

    public function editCustomers(Request $request)
    {
        $request->validate([
            "companey_name" => "required",
            "url" => "required"
        ]);

        $customer = User::findOrFail($request->id);
        $customer->companey_name = $request->input("companey_name");
        $customer->url = $request->input("url");
        $customer->save();

        return redirect()->route('customers')->with('success', 'Customer updated successfully.');
    }

    public function deleteCustomers(Request $request)
    {
        User::findOrFail($request->id)->delete();
        return redirect()->route('customers')->with('success', 'Customer deleted successfully.');
    }
}
