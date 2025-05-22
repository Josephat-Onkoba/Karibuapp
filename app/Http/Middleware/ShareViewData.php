<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Participant;
use App\Models\ConferenceDay;
use Illuminate\Support\Facades\DB;

class ShareViewData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only share data if user is authenticated
        if (Auth::check()) {
            $pendingCheckIns = 0;
            $today = ConferenceDay::getToday();
            
            // If user is an usher and there's a conference today, calculate pending check-ins
            if (Auth::user()->role === 'usher' && $today) {
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
            
            View::share('globalPendingCheckIns', $pendingCheckIns);
            View::share('globalToday', $today);
        }
        
        return $next($request);
    }
} 