<?php

namespace App\Http\Controllers\Usher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\ConferenceDay;
use App\Models\CheckIn;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckInController extends Controller
{
    /**
     * Display the check-in form.
     */
    public function index()
    {
        // Get today's conference day
        $today = ConferenceDay::getToday();
        
        // Get all active conference days for selecting other days
        $days = ConferenceDay::where('active', true)
            ->orderBy('date')
            ->get();
        
        return view('usher.check-in.index', compact('today', 'days'));
    }
    
    /**
     * Process a check-in for a participant.
     */
    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'conference_day_id' => 'required|exists:conference_days,id',
            'notes' => 'nullable|string',
            'redirect_to' => 'nullable|string|in:my-registrations,check-in,participant_view',
            'participant_id_redirect' => 'nullable|exists:participants,id',
        ]);
        
        // Check if the participant is already checked in for this day
        $alreadyCheckedIn = CheckIn::where('participant_id', $validated['participant_id'])
            ->where('conference_day_id', $validated['conference_day_id'])
            ->exists();
        
        if ($alreadyCheckedIn) {
            return redirect()->back()->with('error', 'Participant is already checked in for this day.');
        }
        
        try {
            DB::beginTransaction();
            
            $participant = Participant::with('tickets')->find($validated['participant_id']);
            $conferenceDay = ConferenceDay::find($validated['conference_day_id']);
            
            // Create the check-in record
            $checkIn = CheckIn::create([
                'participant_id' => $validated['participant_id'],
                'conference_day_id' => $validated['conference_day_id'],
                'checked_by_user_id' => Auth::id(),
                'checked_in_at' => now(),
                'notes' => $validated['notes'] ?? null
            ]);
            
            // Get the existing active ticket
            $activeTicket = Ticket::where('participant_id', $participant->id)
                          ->where('active', true)
                          ->first();
            
            // Based on conference day, get the field to check
            $dayField = 'day' . $conferenceDay->id . '_valid';
            
            // Check if we need to create a new ticket:
            // 1. If no active ticket exists
            // 2. If active ticket exists but is not valid for the current day
            // 3. If active ticket exists but has expired
            $needNewTicket = !$activeTicket || !$activeTicket->$dayField || $activeTicket->isExpired();
            
            if ($needNewTicket) {
                // If there's an existing active ticket, mark it as inactive
                if ($activeTicket) {
                    $activeTicket->active = false;
                    $activeTicket->save();
                    
                    Log::info('Existing ticket marked inactive as not valid for day ' . $conferenceDay->id . ' or expired', [
                        'participant_id' => $participant->id,
                        'old_ticket_number' => $activeTicket->ticket_number,
                        'day' => $conferenceDay->id,
                        'was_expired' => $activeTicket->isExpired(),
                        'day_valid' => $activeTicket->$dayField
                    ]);
                }
                
                // Create a new ticket valid only for the current check-in day
                $newTicket = new Ticket();
                $newTicket->ticket_number = Ticket::generateTicketNumber();
                $newTicket->participant_id = $participant->id;
                $newTicket->registered_by_user_id = Auth::id();
                
                // Set all day fields to false by default
                $newTicket->day1_valid = false;
                $newTicket->day2_valid = false;
                $newTicket->day3_valid = false;
                
                // Set the current day to true
                $newTicket->$dayField = true;
                
                $newTicket->active = true;
                
                // Calculate and set expiration date (10:00 PM EAT on the check-in day)
                $newTicket->expiration_date = Ticket::calculateExpirationDate(
                    $newTicket->day1_valid,
                    $newTicket->day2_valid,
                    $newTicket->day3_valid
                );
                
                $newTicket->save();
                
                // Use this new ticket for notifications
                $ticket = $newTicket;
                
                Log::info('New day-specific ticket generated on check-in for day ' . $conferenceDay->id, [
                    'participant_id' => $participant->id,
                    'ticket_number' => $ticket->ticket_number,
                    'day' => $conferenceDay->id,
                    'expiration_date' => $ticket->expiration_date
                ]);
            } else {
                $ticket = $activeTicket;
                Log::info('Using existing ticket for check-in on day ' . $conferenceDay->id, [
                    'participant_id' => $participant->id,
                    'ticket_number' => $ticket->ticket_number,
                    'day' => $conferenceDay->id
                ]);
            }
            
            DB::commit();
            
            // Send ticket notifications (email and SMS)
            try {
                Http::post(route('usher.check-in.send-ticket', ['participantId' => $participant->id]));
            } catch (\Exception $e) {
                // Log the error but don't fail the check-in process
                Log::error('Failed to send ticket notifications', [
                    'participant_id' => $participant->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            // Determine where to redirect based on the redirect_to parameter
            if (isset($validated['redirect_to'])) {
                if ($validated['redirect_to'] === 'my-registrations') {
                    return redirect()->route('usher.my-registrations')
                        ->with('success', "{$participant->full_name} has been checked in successfully for day {$conferenceDay->id}.");
                } elseif ($validated['redirect_to'] === 'participant_view' && isset($validated['participant_id_redirect'])) {
                    return redirect()->route('usher.participant.view', $validated['participant_id_redirect'])
                        ->with('success', "{$participant->full_name} has been checked in successfully for day {$conferenceDay->id}.");
                }
            }
            
            return redirect()->back()
                ->with('success', "{$participant->full_name} has been checked in successfully for day {$conferenceDay->id}.");
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Check-in failed', [
                'participant_id' => $validated['participant_id'],
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Check-in failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Search for a participant by email or name.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query) || strlen($query) < 3) {
            return response()->json([
                'participants' => []
            ]);
        }
        
        $participants = Participant::where('email', 'like', "%{$query}%")
            ->orWhere('full_name', 'like', "%{$query}%")
            ->orWhere('phone_number', 'like', "%{$query}%")
            ->limit(10)
            ->get();
            
        // Add ticket information to each participant
        foreach ($participants as $participant) {
            // Get all tickets for the participant
            $allTickets = Ticket::where('participant_id', $participant->id)
                ->orderBy('created_at', 'desc')
                ->get();
                
            // Get the active ticket if one exists
            $activeTicket = $allTickets->where('active', true)->first();
                
            if ($activeTicket) {
                $participant->has_active_ticket = true;
                $participant->ticket_number = $activeTicket->ticket_number;
                $participant->ticket_valid_for_days = $activeTicket->validForDays();
                $participant->ticket_status = $activeTicket->getValidityStatus();
                $participant->ticket_expired = $activeTicket->isExpired();
                $participant->ticket_expiration_date = $activeTicket->expiration_date ? $activeTicket->expiration_date->format('M d, Y h:i A') : null;
            } elseif ($allTickets->count() > 0) {
                $latestTicket = $allTickets->first();
                $participant->has_active_ticket = false;
                $participant->ticket_number = $latestTicket->ticket_number;
                $participant->ticket_status = 'Inactive';
                $participant->ticket_expired = true;
            } else {
                $participant->has_active_ticket = false;
                $participant->ticket_status = 'No ticket';
                $participant->ticket_expired = false;
            }
            
            // Include ticket count information
            $participant->ticket_count = $allTickets->count();
            $participant->active_ticket_count = $allTickets->where('active', true)->count();
            $participant->has_multiple_tickets = $allTickets->count() > 1;
            
            // Include link to view all tickets
            $participant->view_tickets_url = route('usher.participant.view', $participant->id);
        }
        
        return response()->json([
            'participants' => $participants
        ]);
    }
    
    /**
     * View all check-ins for a specific day.
     */
    public function viewCheckIns(Request $request)
    {
        $dayId = $request->input('day_id', null);
        
        if ($dayId) {
            $day = ConferenceDay::findOrFail($dayId);
        } else {
            $day = ConferenceDay::getToday();
        }
        
        // Get all active conference days for the dropdown
        $days = ConferenceDay::where('active', true)
            ->orderBy('date')
            ->get();
        
        // If no day is found (no conference today and no specific day selected)
        if (!$day) {
            return view('usher.check-in.view', compact('days', 'day'));
        }
        
        // Get all check-ins for the selected day with participant information
        $checkIns = CheckIn::where('conference_day_id', $day->id)
            ->with('participant')
            ->orderBy('checked_in_at', 'desc')
            ->paginate(20);
        
        return view('usher.check-in.view', compact('day', 'days', 'checkIns'));
    }
} 