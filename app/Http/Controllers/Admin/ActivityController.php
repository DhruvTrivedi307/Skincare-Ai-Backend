<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function activity_logs()
    {
        $logs = ActivityLog::paginate(10);
        return view("activity-logs", compact("logs"));
    }
}
