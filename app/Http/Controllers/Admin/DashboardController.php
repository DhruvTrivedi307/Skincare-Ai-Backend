<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkinAnalysis;
use App\Models\Subscriptions;
use App\Models\UsageMetaData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function fetch_admin() 
    {
        if (Auth::user()->role === 'Super-Admin') {
            $data = SkinAnalysis::paginate(10);
            $subscription = User::where('role', 'Admin')->count();
            $users = SkinAnalysis::distinct('user_token')->count('user_token');
            $scans = SkinAnalysis::count();
            $token_usage = SkinAnalysis::sum('token_usage');
            $expenses = 
                UsageMetaData::sum('prompt_token_count') / 1000000 * 1.25 + 
                UsageMetaData::sum('candidates_token_count') / 1000000 * 10;
        } else {
            $admin_token = User::where('token', Auth::user()->token)->first();
            $admin_id = $admin_token->id;
            $data = SkinAnalysis::where('admin_id', $admin_id)->paginate(10);
            $subscription = null;
            $users = null;
            $scans = null;
            $token_usage = null;
            $expenses = null;
        }

        if (Auth::user()->role === 'Super-Admin') {
            $monthlyData = SkinAnalysis::selectRaw('
                MONTH(created_at) as month,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed
            ')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');
        } else {
            $admin_token = User::where('token', Auth::user()->token)->first();
            $admin_id = $admin_token->id;
            $monthlyData = SkinAnalysis::selectRaw('
                MONTH(created_at) as month,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed
            ')
                ->where('admin_id', $admin_id)
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');
        }

        $completed = [];
        $failed = [];

        for ($i = 1; $i <= 12; $i++) {
            $completed[] = isset($monthlyData[$i]) ? $monthlyData[$i]->completed : 0;
            $failed[] = isset($monthlyData[$i]) ? $monthlyData[$i]->failed : 0;
        }

        return view("dashboard", compact("data", "completed", "failed", 'subscription', 'scans', 'token_usage', 'expenses', 'users'));
    }

    public function monthlyStates()
    {
        $states = DB::table('skin_analyses')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->get();

        return response()->json($states);
    }

    public function profile()
    {
        return view("profile");
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:255',
        ]);

        $user = User::find(Auth::id());
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->company_name = $request->company_name;
        $user->url = $request->url;

        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::find(Auth::id());

        if (!password_verify($request->password, $user->password)) {
            return redirect()->route('profile')->with('error', 'Current password is incorrect.');
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return redirect()->route('profile')->with('success', 'Password updated successfully.');
    }

    public function usage_metadata()
    {
        $metadata = UsageMetaData::paginate(10);
        return view("usage-metadata", compact("metadata"));
    }

    public function subscriptions(){
        $subscriptions = Subscriptions::paginate(10);
        return view("subscriptions", compact("subscriptions"));
    }

    public function add_subscriptions(Request $request){
        $request->validate([
            'type' => 'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|string',
            'scans' => 'required|integer',
            'tokens' => 'required|integer',
        ]);

        $subscription = new Subscriptions();
        $subscription->type = $request->type;
        $subscription->price = $request->price;
        $subscription->duration = $request->duration;
        $subscription->scans = $request->scans;
        $subscription->tokens = $request->tokens;
        $subscription->save();

        return redirect()->route('subscriptions')->with('success', 'Subscription plan added successfully.');
    }

    public function update_subscriptions(Request $request, $id){
        $request->validate([
            'type' => 'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|string',
            'scans' => 'required|integer',
            'tokens' => 'required|integer',
        ]);

        $subscription = Subscriptions::find($id);
        if (!$subscription) {
            return redirect()->route('subscriptions')->with('error', 'Subscription plan not found.');
        }

        $subscription->type = $request->type;
        $subscription->price = $request->price;
        $subscription->duration = $request->duration;
        $subscription->scans = $request->scans;
        $subscription->tokens = $request->tokens;
        $subscription->save();

        return redirect()->route('subscriptions')->with('success', 'Subscription plan updated successfully.');
    }

    public function delete_subscriptions($id){
        $subscription = Subscriptions::find($id);
        if (!$subscription) {
            return redirect()->route('subscriptions')->with('error', 'Subscription plan not found.');
        }

        $subscription->delete();

        return redirect()->route('subscriptions')->with('success', 'Subscription plan deleted successfully.');
    }

    public function coupon_codes(){
        $coupons = DB::table('coupon_codes')->paginate(10);
        return view("coupon-codes", compact("coupons"));
    }

    public function add_coupon_code(Request $request){
        $request->validate([
            'code' => 'required|string|unique:coupon_codes,code',
            'description' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric',
            'discount_amount' => 'nullable|numeric',
            'sub_total_limit' => 'nullable|numeric',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'usage_limit' => 'nullable|integer',
        ]);

        DB::table('coupon_codes')->insert([
            'code' => $request->code,
            'description' => $request->description,
            'discount_percentage' => $request->discount_percentage,
            'discount_amount' => $request->discount_amount,
            'sub_total_limit' => $request->sub_total_limit,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'usage_limit' => $request->usage_limit,
        ]);

        return redirect()->route('coupon-codes')->with('success', 'Coupon code added successfully.');
    }

    public function update_coupon_code(Request $request, $id){
        $request->validate([
            'code' => 'required|string|unique:coupon_codes,code,' . $id,
            'description' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric',
            'discount_amount' => 'nullable|numeric',
            'sub_total_limit' => 'nullable|numeric',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'usage_limit' => 'nullable|integer',
        ]);

        DB::table('coupon_codes')->where('id', $id)->update([
            'code' => $request->code,
            'description' => $request->description,
            'discount_percentage' => $request->discount_percentage,
            'discount_amount' => $request->discount_amount,
            'sub_total_limit' => $request->sub_total_limit,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'usage_limit' => $request->usage_limit,
            'updated_at' => now(),
        ]);

        return redirect()->route('coupon-codes')->with('success', 'Coupon code updated successfully.');
    }

    public function delete_coupon_code($id){
        DB::table('coupon_codes')->where('id', $id)->delete();
        return redirect()->route('coupon-codes')->with('success', 'Coupon code deleted successfully.');
    }
    
}
