<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Participant;
use App\Models\CheckIn;
use App\Models\ConferenceDay;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // User statistics
        $usherCount = User::where('role', 'usher')->count();
        $adminCount = User::where('role', 'admin')->count();
        
        // Participant statistics
        $totalParticipants = Participant::count();
        $todayRegistrations = Participant::whereDate('created_at', Carbon::today())->count();
        
        // Check-in statistics
        $totalCheckIns = CheckIn::count();
        $todayCheckIns = CheckIn::whereDate('created_at', Carbon::today())->count();
        $pendingCheckIns = Participant::whereDoesntHave('checkIns')->count();
        
        // Role distribution
        $roleDistribution = Participant::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->orderBy('count', 'desc')
            ->get();
        
        // Get current conference day
        $today = ConferenceDay::whereDate('date', Carbon::today())->first();
        
        // Registration trend for the last 7 days
        $registrationTrend = Participant::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(function ($item) {
                return $item->count;
            })
            ->toArray();
            
        // Check-in trend for the last 7 days
        $checkInTrend = CheckIn::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(function ($item) {
                return $item->count;
            })
            ->toArray();
            
        // Get the latest 10 check-ins with usher information
        $latestCheckIns = CheckIn::with(['participant', 'checkedBy'])
            ->latest()
            ->take(10)
            ->get();
            
        // Usher performance (check-ins per usher)
        $usherPerformance = CheckIn::select('checked_by_user_id', DB::raw('COUNT(*) as count'))
            ->with('checkedBy:id,name')
            ->groupBy('checked_by_user_id')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();
            
        // Payment statistics
        $paidParticipants = Participant::whereNotNull('payment_status')
            ->where('payment_status', 'paid')
            ->count();
        $paymentRate = $totalParticipants > 0 ? round(($paidParticipants / $totalParticipants) * 100) : 0;
        
        return view('admin.dashboard.index', [
            // User counts
            'usherCount' => $usherCount,
            'adminCount' => $adminCount,
            
            // Participant statistics
            'totalParticipants' => $totalParticipants,
            'todayRegistrations' => $todayRegistrations,
            'todayCheckIns' => $todayCheckIns,
            'totalCheckIns' => $totalCheckIns,
            'pendingCheckIns' => $pendingCheckIns,
            
            // Payment statistics
            'paidParticipants' => $paidParticipants,
            'paymentRate' => $paymentRate,
            
            // Data for charts
            'roleDistribution' => $roleDistribution,
            'registrationTrend' => $registrationTrend,
            'checkInTrend' => $checkInTrend,
            
            // Latest activity
            'latestCheckIns' => $latestCheckIns,
            'usherPerformance' => $usherPerformance,
            
            // Conference info
            'today' => $today,
        ]);
    }
    
    /**
     * Filter dashboard data based on parameters
     */
    public function filter()
    {
        $startDate = request('start_date') ? Carbon::parse(request('start_date')) : Carbon::now()->subDays(30);
        $endDate = request('end_date') ? Carbon::parse(request('end_date')) : Carbon::now();
        $usher = request('usher_id');
        $role = request('role');
        
        // Base queries
        $participantsQuery = Participant::whereBetween('created_at', [$startDate, $endDate]);
        $checkInsQuery = CheckIn::whereBetween('created_at', [$startDate, $endDate]);
        
        // Apply filters
        if ($usher) {
            $participantsQuery->where('registered_by_user_id', $usher);
            $checkInsQuery->where('user_id', $usher);
        }
        
        if ($role) {
            $participantsQuery->where('role', $role);
        }
        
        // Get results
        $filteredParticipantCount = $participantsQuery->count();
        $filteredCheckInCount = $checkInsQuery->count();
        
        // Role distribution with filters
        $filteredRoleDistribution = $participantsQuery->clone()
            ->selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->orderBy('count', 'desc')
            ->get();
            
        return response()->json([
            'participantCount' => $filteredParticipantCount,
            'checkInCount' => $filteredCheckInCount,
            'roleDistribution' => $filteredRoleDistribution,
            'dateRange' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
        ]);
    }
} 