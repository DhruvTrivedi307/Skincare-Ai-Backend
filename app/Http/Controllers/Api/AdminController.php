<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customers;
use App\Models\SkinAnalysis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function fetch()
    {
        $data = SkinAnalysis::paginate(10);

        $states = DB::table('skin_analyses')
            ->selectRaw('
                MONTH(created_at) as month,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as success,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failure
            ')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        return view("index", compact("data", "states"));
    }

    public function getResult($id){
        $analysis = SkinAnalysis::where("user_token", $id)->get();
        $token = request()->header("auth");
        return response()->json([
            'token' => $token,
            'data' => $analysis,
        ]);
    }

    public function getAllAnalysis(Request $request){
        $token = $request->header('auth');
        $user = User::where('token',$token)->first();
        $analysis = SkinAnalysis::where("admin_id",$user->id)->get();
        return response()->json([
            'token' => $token,
            'id' => $user->id,
            'user' => $user->email,
            'data' => $analysis
        ]);
    }

    public function getUsers(Request $request){
        $admin_token = $request->header('auth');
        $user = User::where('token',$admin_token)->first();
        $admin_id = $user->id;
        $users = SkinAnalysis::where('admin_id',$admin_id)->distinct('user_token')->count('user_token');
        return response()->json([
            "user" => $user->email,
            "token" => $admin_token,
            "total users" => $users
        ]);
    }

    public function getScans(Request $request){
        $admin_token = $request->header('auth');
        $user = User::where('token',$admin_token)->first();
        $admin_id = $user->id;
        $scans = SkinAnalysis::where('admin_id',$admin_id)->count();
        return response()->json([
            "user" => $user->email,
            "token" => $admin_token,
            "total scans" => $scans
        ]);
    }

    public function getTokenUsage(Request $request){
        $admin_token = $request->header('auth');
        $user = User::where('token',$admin_token)->first();
        $admin_id = $user->id;
        $token_usage = SkinAnalysis::where('admin_id',$admin_id)->sum('token_usage');
        return response()->json([
            "user" => $user->email,
            "token" => $admin_token,
            "total token usage" => $token_usage
        ]);
    }
}
