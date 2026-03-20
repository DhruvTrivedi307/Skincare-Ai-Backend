<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkinAnalysis;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function fetch(Request $request){

        $token = $request->header("token");
        
        if (!$token) {
            return response()->json([
                "error" => "Missing token"
            ], 400);
        }

        $found = SkinAnalysis::where("token", $token)->get();

        if($found->isEmpty()){
            return response()->json(
                ["error" => "Incorrect token or Data not found"], 400
            );
        }

        return response()->json($found);

    }
}
