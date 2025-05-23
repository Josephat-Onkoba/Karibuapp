<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $usherCount = User::where('role', 'usher')->count();
        
        return view('admin.dashboard.index', [
            'usherCount' => $usherCount,
            'todayVisitorsCount' => 0, // To be implemented
            'totalVisitorsCount' => 0, // To be implemented
        ]);
    }
} 