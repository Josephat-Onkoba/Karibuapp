<?php

namespace App\Http\Controllers\Usher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Participant;
use App\Models\ConferenceDay;
use App\Models\CheckIn;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get today's conference day if available
        $today = ConferenceDay::getToday();
        
        // Count of check-ins today
        $todayVisitorsCount = 0;
        
        // Count of registrations by this usher
        $registeredByYou = Participant::where('registered_by_user_id', Auth::id())->count();
        
        // Count of pending check-ins (participants who are registered with valid tickets for today but not checked in)
        $pendingCheckIns = 0;
        
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
        
        return view('usher.dashboard.index', [
            'todayVisitorsCount' => $todayVisitorsCount,
            'registeredByYou' => $registeredByYou,
            'pendingCheckIns' => $pendingCheckIns,
            'today' => $today
        ]);
    }
} 