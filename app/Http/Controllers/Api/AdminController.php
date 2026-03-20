<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customers;
use App\Models\SkinAnalysis;
use Illuminate\Http\Request;
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
}
