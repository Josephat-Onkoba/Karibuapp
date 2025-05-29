<?php

namespace App\Http\Controllers\Usher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Participant,
    ConferenceDay,
    CheckIn,
    Ticket,
    User
};
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketMail;
use Illuminate\Support\Facades\Session;
use App\Services\TalkSasaSmsService;

class CheckInController extends Controller
{
    protected $smsService;

    public function __construct(TalkSasaSmsService $smsService)
    {
        $this->smsService = $smsService;
    }
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
        // Get today's conference day
        $today = ConferenceDay::getToday();
        
        $validated = $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'conference_day_id' => [
                'required',
                'exists:conference_days,id',
                function ($attribute, $value, $fail) {
                    $conferenceDay = ConferenceDay::find($value);
                    $today = now()->startOfDay();
                    
                    // Check if the selected day is in the future
                    if ($conferenceDay->date->startOfDay() > $today) {
                        $fail('Cannot check in for a future day. Please select today\'s date.');
                    }
                    
                    // Optional: Check if the day is too far in the past (adjust days as needed)
                    if ($conferenceDay->date->startOfDay() < $today->copy()->subDays(1)) {
                        $fail('Cannot check in for a day that has already passed.');
                    }
                },
            ],
            'notes' => 'nullable|string',
            'redirect_to' => 'nullable|string|in:my-registrations,check-in,participant_view',
            'participant_id_redirect' => 'nullable|exists:participants,id',
        ]);
        
        // Get the conference day
        $conferenceDay = ConferenceDay::findOrFail($validated['conference_day_id']);
        $today = now()->startOfDay();
        
        // Check if trying to check in for today
        if ($conferenceDay->date->startOfDay() != $today) {
            return back()
                ->withInput()
                ->with('error', 'You can only check in participants for today.');
        }
        
        // Check if the participant is already checked in for this day
        $alreadyCheckedIn = CheckIn::where('participant_id', $validated['participant_id'])
            ->where('conference_day_id', $validated['conference_day_id'])
            ->exists();
        
        if ($alreadyCheckedIn) {
            return back()
                ->withInput()
                ->with('error', 'This participant is already checked in for this day.');
        }
        
        try {
            // Get the participant with relationships
            $participant = Participant::with(['ticket', 'checkIns'])
                ->findOrFail($validated['participant_id']);
                
            // Get the conference day
            $conferenceDay = ConferenceDay::findOrFail($validated['conference_day_id']);
            
            // Ensure we have model instances, not collections
            if (!$participant instanceof \App\Models\Participant) {
                throw new \RuntimeException('Invalid participant data');
            }
            
            if (!$conferenceDay instanceof \App\Models\ConferenceDay) {
                throw new \RuntimeException('Invalid conference day data');
            }
            
            // Check payment eligibility for the day
            $paymentEligibility = $this->checkPaymentEligibility($participant, $conferenceDay);
            
            if (!$paymentEligibility['eligible']) {
                // Store necessary data in session for payment
                Session::put('additional_day_payment', [
                    'participant_id' => $participant->id,
                    'conference_day_id' => $conferenceDay->id,
                    'current_days' => $participant->eligible_days,
                    'required_payment' => $this->calculateRequiredPayment($participant),
                    'return_to' => $request->get('redirect_to', 'check-in'),
                    'participant_id_redirect' => $request->get('participant_id_redirect')
                ]);
                
                return redirect()->route('usher.registration.additional_day_payment')
                    ->with('warning', $paymentEligibility['message']);
            }
            
            DB::beginTransaction();
            
            // Check if participant has any ticket history
            $hasTicketHistory = $participant->ticket()->exists();
            
            // Check if already checked in for this specific day
            $alreadyCheckedInToday = CheckIn::where('participant_id', $participant->id)
                ->where('conference_day_id', $conferenceDay->id)
                ->exists();
                
            // Get or create appropriate ticket - ensure we're passing model instances
            $ticket = $this->getOrCreateTicket($participant, $conferenceDay);
            
            // Verify we got a valid ticket instance
            if (!$ticket instanceof \App\Models\Ticket) {
                throw new \RuntimeException('Failed to get or create ticket');
            }
            
            // Send notifications for first check-in or when checking in on a new day
            if (!$hasTicketHistory || !$alreadyCheckedInToday) {
                // Ensure we have the latest ticket data with relationships
                $ticket->load(['participant']);
                
                // Send ticket via email
                try {
                    $mail = new TicketMail($ticket);
                    Mail::to($participant->email)->send($mail);
                        
                    Log::info('Ticket email sent to participant', [
                        'participant_id' => $participant->id,
                        'email' => $participant->email,
                        'ticket_number' => $ticket->ticket_number,
                        'conference_day' => $conferenceDay->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send ticket email: ' . $e->getMessage());
                }
                
                // Send SMS notification using the notification system
                try {
                    // The TicketSmsNotification will handle the phone number validation
                    $participant->notify(new \App\Notifications\TicketSmsNotification($ticket));
                    
                    Log::info('Ticket SMS notification queued for sending', [
                        'participant_id' => $participant->id,
                        'phone' => $participant->phone_number,
                        'ticket_number' => $ticket->ticket_number,
                        'conference_day' => $conferenceDay->id,
                        'is_first_checkin' => !$hasTicketHistory
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send ticket SMS notification: ' . $e->getMessage(), [
                        'participant_id' => $participant->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        
        // Create the check-in record
            CheckIn::create([
            'participant_id' => $validated['participant_id'],
            'conference_day_id' => $validated['conference_day_id'],
            'checked_by_user_id' => Auth::id(),
            'checked_in_at' => now(),
            'notes' => $validated['notes'] ?? null
        ]);
        
            DB::commit();
            
            // Handle redirect based on source
            $redirectRoute = match($request->get('redirect_to')) {
                'my-registrations' => 'usher.registration.my-registrations',
                'registration.my-registrations' => 'usher.registration.my-registrations',
                'participant_view' => 'usher.participant.view', // Correct route name as per web.php
                default => 'usher.check-in'
            };
            
            // Log the redirect information for debugging
            Log::info('Check-in redirect', [
                'redirect_to' => $request->get('redirect_to'),
                'resolved_route' => $redirectRoute
            ]);
            
            if ($request->get('redirect_to') === 'participant_view' && $request->get('participant_id_redirect')) {
                // Use the id parameter name as per the route definition
                return redirect()->route($redirectRoute, ['id' => $request->get('participant_id_redirect')])
                    ->with('success', 'Participant checked in successfully.');
            }
            
            return redirect()->route($redirectRoute)
                ->with('success', 'Participant checked in successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Check-in failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to check in participant. ' . $e->getMessage());
        }
    }
    
    /**
     * Check payment eligibility for a specific day
     */
    /**
     * Check payment eligibility for a participant on a specific conference day.
     *
     * @param \App\Models\Participant $participant
     * @param \App\Models\ConferenceDay $conferenceDay
     * @return array
     */
    private function checkPaymentEligibility(\App\Models\Participant $participant, \App\Models\ConferenceDay $conferenceDay): array
    {
        // If participant category doesn't require payment
        if (in_array($participant->category, ['invited', 'internal', 'coordinators'])) {
            return ['eligible' => true, 'message' => null];
        }
        
        // For general participants, check if they've paid for enough days
        if ($participant->category === 'general') {
            $paidDays = $participant->eligible_days ?? 0;
            
            // Get check-in history
            $checkInHistory = $participant->checkIns()
                ->orderBy('conference_day_id')
                ->pluck('conference_day_id')
                ->toArray();
            
            // Add current day to sequence for validation
            $checkInSequence = array_merge($checkInHistory, [$conferenceDay->id]);
            sort($checkInSequence);
            
            // Check if this is a consecutive day
            $isConsecutiveDay = empty($checkInHistory) || 
                (end($checkInHistory) + 1 === $conferenceDay->id);
            
            // Count used days
            $usedDays = count($checkInHistory);
            
            // If they've used all their paid days
            if ($usedDays >= $paidDays) {
                return [
                    'eligible' => false,
                    'message' => "Participant has only paid for {$paidDays} day(s) and has already used {$usedDays} day(s). Additional payment required for day {$conferenceDay->id}."
                ];
            }
            
            // If trying to attend a non-consecutive day
            if (!$isConsecutiveDay && !empty($checkInHistory)) {
                $lastAttendedDay = end($checkInHistory);
                return [
                    'eligible' => false,
                    'message' => "Non-consecutive day attendance detected. Last attended day {$lastAttendedDay}. Payment required for day {$conferenceDay->id}."
                ];
            }
        }
        
        // For exhibitors and presenters, they should have full conference access
        if (in_array($participant->category, ['exhibitor', 'presenter']) && $participant->payment_confirmed) {
            return ['eligible' => true, 'message' => null];
        }
        
        // If payment is not confirmed
        if (!$participant->payment_confirmed) {
            return [
                'eligible' => false,
                'message' => 'Payment not confirmed. Please process payment before check-in.'
            ];
        }
        
        return ['eligible' => true, 'message' => null];
    }
    
    /**
     * Calculate required payment for additional day
     */
    /**
     * Calculate the required payment for a participant.
     *
     * @param \App\Models\Participant $participant
     * @return float
     */
    private function calculateRequiredPayment(\App\Models\Participant $participant): float
    {
        // Base payment for one day
        return match($participant->category) {
            'general' => 3000.00, // One day rate for general participants
            'exhibitor' => 30000.00,
            'presenter' => match($participant->presenter_type) {
                'student' => 4000.00,
                'non_student' => 6000.00,
                'international' => 100.00, // USD
                default => 6000.00
            },
            default => 0.00
        };
    }
    
    /**
     * Get existing valid ticket or create new one
     */
    /**
     * Get an existing valid ticket or create a new one.
     *
     * @param \App\Models\Participant $participant
     * @param \App\Models\ConferenceDay $conferenceDay
     * @return \App\Models\Ticket
     */
    private function getOrCreateTicket(\App\Models\Participant $participant, \App\Models\ConferenceDay $conferenceDay): \App\Models\Ticket
    {
            // Get the existing active ticket
            $activeTicket = Ticket::where('participant_id', $participant->id)
                          ->where('active', true)
                          ->first();
            
            // Based on conference day, get the field to check
            $dayField = 'day' . $conferenceDay->id . '_valid';
            
        // Get participant's check-in history
        $checkInHistory = CheckIn::where('participant_id', $participant->id)
            ->orderBy('conference_day_id')
            ->pluck('conference_day_id')
            ->toArray();
            
        // Add current day to history for validation
        $checkInSequence = array_merge($checkInHistory, [$conferenceDay->id]);
        sort($checkInSequence);
        
        // Check if this is a consecutive day check-in
        $isConsecutiveDay = empty($checkInHistory) || 
            (end($checkInHistory) + 1 === $conferenceDay->id);
            
        // Determine if we need a new ticket
        $needNewTicket = false;
        
        if (!$activeTicket) {
            // No active ticket exists
            $needNewTicket = true;
        } elseif (!$isConsecutiveDay) {
            // Non-consecutive day attendance requires new ticket
            $needNewTicket = true;
        } elseif ($activeTicket->isExpired()) {
            // Expired ticket needs replacement
            $needNewTicket = true;
        }
            
            if ($needNewTicket) {
                // If there's an existing active ticket, mark it as inactive
                if ($activeTicket) {
                    $activeTicket->active = false;
                    $activeTicket->save();
                    
                Log::info('Existing ticket marked inactive', [
                        'participant_id' => $participant->id,
                        'old_ticket_number' => $activeTicket->ticket_number,
                    'reason' => $isConsecutiveDay ? 'expired' : 'non_consecutive_day'
                    ]);
                }
                
            // Create a new ticket
                $newTicket = new Ticket();
                $newTicket->ticket_number = Ticket::generateTicketNumber();
                $newTicket->participant_id = $participant->id;
                $newTicket->registered_by_user_id = Auth::id();
                
                // Set all day fields to false by default
                $newTicket->day1_valid = false;
                $newTicket->day2_valid = false;
                $newTicket->day3_valid = false;
                
            // Set the current day as valid
                $newTicket->$dayField = true;
                $newTicket->active = true;
                
            // Calculate expiration date based on consecutive days
            $newTicket->expiration_date = now()->endOfDay();
                
                $newTicket->save();
            
            Log::info('New ticket generated', [
                'participant_id' => $participant->id,
                'ticket_number' => $newTicket->ticket_number,
                'conference_day' => $conferenceDay->id,
                'is_consecutive' => $isConsecutiveDay,
                'check_in_sequence' => $checkInSequence
            ]);
            
            return $newTicket;
        } else {
            // Update existing ticket for consecutive day
            $activeTicket->$dayField = true;
            $activeTicket->save();
            
            Log::info('Using existing ticket for consecutive day', [
                'participant_id' => $participant->id,
                'ticket_number' => $activeTicket->ticket_number,
                'conference_day' => $conferenceDay->id,
                'check_in_sequence' => $checkInSequence
            ]);
            
            return $activeTicket;
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