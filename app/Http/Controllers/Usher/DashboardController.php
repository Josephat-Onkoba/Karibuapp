<?php

namespace App\Http\Controllers\Usher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Participant;
use App\Models\ConferenceDay;
use App\Models\CheckIn;
use App\Models\Ticket;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get today's conference day if available
        $today = ConferenceDay::getToday();
        $conferenceDays = ConferenceDay::orderBy('date')->get();
        
        // Count of check-ins today
        $todayVisitorsCount = 0;
        
        // Count of registrations by this usher
        $registeredByYou = Participant::where('registered_by_user_id', Auth::id())->count();
        
        // Count of pending check-ins (participants who are registered with valid tickets for today but not checked in)
        $pendingCheckIns = 0;
        
        // Get registration trends (last 7 days)
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $registrationTrend = Participant::where('registered_by_user_id', Auth::id())
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
        
        // Create a complete array of dates (including zeroes for days with no registrations)
        $registrationsByDay = [];
        $dateLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dateLabels[] = Carbon::now()->subDays($i)->format('D');
            $registrationsByDay[] = $registrationTrend->has($date) ? $registrationTrend[$date]->count : 0;
        }
        
        // Get role distribution
        $roleDistribution = Participant::where('registered_by_user_id', Auth::id())
            ->selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->orderBy('count', 'desc')
            ->get();
        
        // Get payment status overview
        $paymentStatusOverview = Participant::where('registered_by_user_id', Auth::id())
            ->selectRaw('payment_status, COUNT(*) as count')
            ->groupBy('payment_status')
            ->orderBy('count', 'desc')
            ->get();
        
        // Recent activities (registrations and check-ins)
        $recentRegistrations = Participant::where('registered_by_user_id', Auth::id())
            ->with('ticket')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get 5 check-ins by this usher
        $recentCheckInsByUsher = CheckIn::where('checked_by_user_id', Auth::id())
            ->with(['participant', 'conferenceDay'])
            ->orderBy('checked_in_at', 'desc')
            ->take(5)
            ->get();
            
        // Get top 10 latest check-ins from all ushers
        $latestCheckIns = CheckIn::with(['participant', 'conferenceDay', 'checkedBy'])
            ->orderBy('checked_in_at', 'desc')
            ->take(10)
            ->get();
        
        if ($today) {
            // Count check-ins for today
            $todayVisitorsCount = CheckIn::where('conference_day_id', $today->id)->count();
            
            // Get participants who have valid tickets for today but are not checked in
            $dayColumn = 'day' . $today->id . '_valid';
            
            // Participants registered by this usher with tickets valid for today but not checked in
            $pendingCheckIns = Participant::where('participants.registered_by_user_id', Auth::id())
                ->join('tickets', 'participants.id', '=', 'tickets.participant_id')
                ->where('tickets.' . $dayColumn, true)
                ->whereNotExists(function ($query) use ($today) {
                    $query->select(DB::raw(1))
                        ->from('check_ins')
                        ->whereRaw('check_ins.participant_id = participants.id')
                        ->where('check_ins.conference_day_id', $today->id);
                })
                ->count();
        }
        
        // System status information
        $systemStatus = [
            'appVersion' => config('app.version', '1.0.0'),
            'lastSync' => Carbon::now()->format('Y-m-d H:i:s'),
            'status' => 'online',
            'networkStatus' => 'connected'
        ];
        
        return view('usher.dashboard.index', [
            'todayVisitorsCount' => $todayVisitorsCount,
            'registeredByYou' => $registeredByYou,
            'pendingCheckIns' => $pendingCheckIns,
            'today' => $today,
            'conferenceDays' => $conferenceDays,
            'registrationsByDay' => $registrationsByDay,
            'dateLabels' => $dateLabels,
            'roleDistribution' => $roleDistribution,
            'paymentStatusOverview' => $paymentStatusOverview,
            'recentRegistrations' => $recentRegistrations,
            'recentCheckIns' => $recentCheckInsByUsher,
            'latestCheckIns' => $latestCheckIns,
            'systemStatus' => $systemStatus,
            'usherName' => Auth::user()->name
        ]);
    }
} 