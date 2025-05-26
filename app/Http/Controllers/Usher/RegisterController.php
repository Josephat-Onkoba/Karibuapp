<?php

namespace App\Http\Controllers\Usher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Participant;
use App\Models\ConferenceDay;
use App\Models\Ticket;
use App\Models\CheckIn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketMail;

class RegisterController extends Controller
{
    /**
     * Show the first step of registration form (Category Selection)
     */
    public function index()
    {
        // Clear any previous session data when starting fresh
        Session::forget('registration_data');
        
        return redirect()->route('usher.registration.step1');
    }
    
    /**
     * Show the first step of registration form (Category Selection)
     */
    public function showStep1()
    {
        // Get the participant categories
        $categories = [
            'general' => 'Delegates',
            'exhibitor' => 'Exhibitors',
            'presenter' => 'Presenters',
            'invited' => 'Invited Guests & Speakers',
            'internal' => 'Internal Participants',
            'coordinators' => 'Session Coordinators'
        ];
        
        $categoryDescriptions = [
            'general' => 'Delegates and other general participants',
            'exhibitor' => 'Exhibition participants (KSH 30,000)',
            'presenter' => 'Conference Presenters (Student: KSH 4,000, Non-Student: KSH 6,000, International: USD 100)',
            'invited' => 'Chief Guests, Guests, Keynote Speakers, Panelists',
            'internal' => 'Staff, Students',
            'coordinators' => 'Secretariat, Moderators, Rapporteurs'
        ];
        
        // Get any previously stored data
        $data = Session::get('registration_data', []);
        
        return view('usher.registration.steps.step1', compact('categories', 'categoryDescriptions', 'data'));
    }
    
    /**
     * Process step 1 (Category Selection)
     */
    public function processStep1(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|in:general,exhibitor,presenter,invited,internal,coordinators',
        ]);
        
        // Store the form data in the session
        $data = Session::get('registration_data', []);
        $data = array_merge($data, $validated);
        Session::put('registration_data', $data);
        
        return redirect()->route('usher.registration.step2');
    }
    
    /**
     * Show the second step of registration form (Participant Details)
     */
    public function showStep2(Request $request)
    {
        // Check if step 1 is completed
        $data = Session::get('registration_data', []);
        if (!isset($data['category'])) {
            return redirect()->route('usher.registration.step1');
        }
        
        // Get roles for the selected category
        $roles = $this->getCategoryRoles($data['category']);
        
        return view('usher.registration.steps.step2', compact('data', 'roles'));
    }
    
    /**
     * Check if a participant already exists
     */
    public function checkExistingParticipant(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'full_name' => 'required|string'
        ]);

        $participant = Participant::where('email', $request->email)
            ->orWhere('full_name', $request->full_name)
            ->with(['ticket' => function($query) {
                $query->where('active', true);
            }])
            ->first();

        if ($participant) {
            return response()->json([
                'exists' => true,
                'participant' => [
                    'id' => $participant->id,
                    'full_name' => $participant->full_name,
                    'email' => $participant->email,
                    'has_active_ticket' => $participant->ticket !== null
                ]
            ]);
        }

        return response()->json(['exists' => false]);
    }
    
    /**
     * Process step 2 (Participant Details)
     */
    public function processStep2(Request $request)
    {
        $data = Session::get('registration_data', []);
        $category = $data['category'] ?? '';
        
        // Basic validation for all categories
        $validationRules = [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'role' => 'required|string|max:255',
        ];
        
        // Add category-specific validation rules
        switch ($category) {
            case 'general':
                $validationRules['payment_status'] = 'required|string|in:Not Paid,Paid via Vabu,Paid via M-Pesa,Waived';
                if ($request->input('payment_status') !== 'Not Paid' && $request->input('payment_status') !== 'Waived') {
                    $validationRules['eligible_days'] = 'required|integer|in:1,2,3';
                }
                break;
                
            case 'exhibitor':
                $validationRules['payment_status'] = 'required|string|in:Not Paid,Paid via Vabu,Paid via M-Pesa';
                break;
                
            case 'presenter':
                $validationRules['presenter_type'] = 'required|string|in:non_student,student,international';
                $validationRules['payment_status'] = 'required|string|in:Not Paid,Paid via Vabu,Paid via M-Pesa';
                break;
                
            case 'internal':
                if ($request->input('role') === 'Student') {
                    $validationRules['student_admission_number'] = 'required|string|max:50';
                } elseif ($request->input('role') === 'Staff') {
                    $validationRules['staff_number'] = 'required|string|max:50';
                }
                break;
        }
        
        $validated = $request->validate($validationRules);
        
        // Check for existing participant
        $fullName = strtolower(trim($validated['full_name']));
        $nameParts = explode(' ', $fullName);
        sort($nameParts);
        $normalizedName = implode(' ', $nameParts);

        $existingParticipant = Participant::where(function($query) use ($validated, $normalizedName) {
            $query->where('email', $validated['email'])
                ->orWhereRaw('LOWER(TRIM(full_name)) = ?', [$normalizedName]);
        })
        ->with(['ticket' => function($query) {
            $query->where('active', true);
        }])
        ->first();

        if ($existingParticipant) {
            $data = array_merge($data, $validated);
            Session::put('registration_data', $data);

            return redirect()->route('usher.registration.step2')
                ->with('warning', 'This participant is already registered.')
                ->with('existing_participant', [
                    'full_name' => $existingParticipant->full_name,
                    'email' => $existingParticipant->email,
                    'has_active_ticket' => $existingParticipant->ticket !== null
                ]);
        }
        
        // Set default values based on category
        switch ($category) {
            case 'exhibitor':
                $validated['eligible_days'] = 3;
                $validated['payment_amount'] = Participant::EXHIBITOR_FEE;
                break;
                
            case 'presenter':
                $validated['eligible_days'] = 3;
                switch ($validated['presenter_type']) {
                    case 'non_student':
                        $validated['payment_amount'] = Participant::PRESENTER_NON_STUDENT_FEE;
                        break;
                    case 'student':
                        $validated['payment_amount'] = Participant::PRESENTER_STUDENT_FEE;
                        break;
                    case 'international':
                        $validated['payment_amount'] = Participant::PRESENTER_INTERNATIONAL_FEE;
                        break;
                }
                break;
                
            case 'general':
                if ($validated['payment_status'] === 'Waived') {
                    $validated['payment_confirmed'] = true;
                    $validated['eligible_days'] = 3;
                } else {
                    $days = $validated['eligible_days'] ?? 1;
                    $validated['payment_amount'] = match($days) {
                        1 => 3000,
                        2 => 6000,
                        3 => 9000,
                        default => 3000,
                    };
                }
                break;
                
            case 'invited':
            case 'coordinators':
                $validated['payment_status'] = 'Not Applicable';
                $validated['payment_confirmed'] = true;
                $validated['eligible_days'] = 3;
                break;
                
            case 'internal':
                $validated['payment_status'] = 'Waived';
                $validated['payment_confirmed'] = true;
                $validated['eligible_days'] = 3;
                break;
        }
        
        // Store the form data in the session
        $data = array_merge($data, $validated);
        Session::put('registration_data', $data);
        
        // If payment is required but not paid, redirect to payment form
        if (in_array($category, ['general', 'exhibitor', 'presenter']) 
            && $validated['payment_status'] === 'Not Paid') {
            return redirect()->route('usher.registration.payment');
        }
        
        return redirect()->route('usher.registration.step3');
    }
    
    /**
     * Show the third step of registration form (Attendance Days)
     */
    public function showStep3()
    {
        // Check if previous steps are completed
        $data = Session::get('registration_data', []);
        if (!isset($data['full_name']) || !isset($data['category'])) {
            return redirect()->route('usher.registration.step1');
        }
        
        // If general category with "Not Paid" status, redirect to payment form
        if ($data['category'] === 'general' && isset($data['payment_status']) && $data['payment_status'] === 'Not Paid') {
            return redirect()->route('usher.registration.payment');
        }
        
        // Get active conference days
        $conferenceDays = ConferenceDay::where('active', true)
            ->orderBy('date')
            ->get();
        
        return view('usher.registration.steps.step3', compact('data', 'conferenceDays'));
    }
    
    /**
     * Process step 3 (Attendance Days)
     */
    public function processStep3(Request $request)
    {
        $validated = $request->validate([
            'attendance_days' => 'required|array|min:1',
            'attendance_days.*' => 'exists:conference_days,id',
        ]);
        
        // Get the selected conference days
        $selectedDays = ConferenceDay::whereIn('id', $validated['attendance_days'])
            ->where('active', true)
            ->get();
            
        // Check if any selected day is in the past
        $pastDays = $selectedDays->filter(function($day) {
            return $day->date->isPast() && !$day->date->isToday();
        });
        
        if ($pastDays->isNotEmpty()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['attendance_days' => 'You cannot select past conference days.']);
        }
        
        // Store the form data in the session
        $data = Session::get('registration_data', []);
        $data['attendance_days'] = $validated['attendance_days'];
        Session::put('registration_data', $data);
        
        return redirect()->route('usher.registration.step4');
    }
    
    /**
     * Show the fourth step of registration form (Ticket Preview)
     */
    public function showStep4()
    {
        // Check if previous steps are completed
        $data = Session::get('registration_data', []);
        if (!isset($data['attendance_days'])) {
            return redirect()->route('usher.registration.step3');
        }
        
        // Get active conference days for display
        $conferenceDays = ConferenceDay::whereIn('id', $data['attendance_days'])
            ->orderBy('date')
            ->get();
        
        return view('usher.registration.steps.step4', compact('data', 'conferenceDays'));
    }
    
    /**
     * Process step 4 (Move to Check-in Decision)
     */
    public function processStep4(Request $request)
    {
        return redirect()->route('usher.registration.step5');
    }
    
    /**
     * Show the fifth step of registration form (Check-in Decision)
     */
    public function showStep5()
    {
        // Check if previous steps are completed
        $data = Session::get('registration_data', []);
        if (!isset($data['attendance_days'])) {
            return redirect()->route('usher.registration.step3');
        }
        
        // Check if today is a valid conference day
        $today = ConferenceDay::getToday();
        $canCheckInToday = $today && in_array($today->id, $data['attendance_days']);
        
        return view('usher.registration.steps.step5', compact('data', 'today', 'canCheckInToday'));
    }
    
    /**
     * Process step 5 (Check-in Decision)
     */
    public function processStep5(Request $request)
    {
        $validated = $request->validate([
            'check_in_today' => 'required|boolean',
        ]);
        
        // Store the form data in the session
        $data = Session::get('registration_data', []);
        $data['check_in_today'] = $validated['check_in_today'];
        Session::put('registration_data', $data);
        
        // Process registration completion directly instead of redirecting
        return $this->complete();
    }
    
    /**
     * Complete the registration process and save all data
     */
    public function complete()
    {
        $data = Session::get('registration_data', []);
        if (!isset($data['category']) || !isset($data['full_name'])) {
            return redirect()->route('usher.registration.step1');
        }
        
        try {
            DB::beginTransaction();
            
            // Create participant
            $participantData = [
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'],
                'job_title' => $data['job_title'] ?? null,
                'organization' => $data['organization'] ?? null,
                'student_admission_number' => $data['student_admission_number'] ?? null,
                'staff_number' => $data['staff_number'] ?? null,
                'role' => $data['role'],
                'category' => $data['category'],
                'registered_by_user_id' => Auth::id(),
            ];
            
            // Set payment data based on category
            switch ($data['category']) {
                case 'general':
                    $participantData['payment_status'] = $data['payment_status'] ?? 'Not Paid';
                    $participantData['payment_confirmed'] = isset($data['payment_confirmed']) && $data['payment_confirmed'];
                    $participantData['payment_amount'] = $data['payment_amount'] ?? null;
                    $participantData['eligible_days'] = $data['eligible_days'] ?? null;
                    break;
                    
                case 'exhibitor':
                    $participantData['payment_status'] = $data['payment_status'] ?? 'Not Paid';
                    $participantData['payment_confirmed'] = isset($data['payment_confirmed']) && $data['payment_confirmed'];
                    $participantData['payment_amount'] = $data['payment_amount'] ?? Participant::EXHIBITOR_FEE;
                    $participantData['eligible_days'] = 3; // Full conference period
                    break;
                    
                case 'presenter':
                    $participantData['payment_status'] = $data['payment_status'] ?? 'Not Paid';
                    $participantData['payment_confirmed'] = isset($data['payment_confirmed']) && $data['payment_confirmed'];
                    $participantData['presenter_type'] = $data['presenter_type'];
                    $participantData['payment_amount'] = $data['payment_amount'] ?? null;
                    $participantData['eligible_days'] = 3; // Full conference period
                    break;
                    
                case 'invited':
                case 'coordinators':
                    $participantData['payment_status'] = 'Not Applicable';
                    $participantData['payment_confirmed'] = true;
                    break;
                    
                case 'internal':
                    $participantData['payment_status'] = 'Waived';
                    $participantData['payment_confirmed'] = true;
                    break;
            }
            
            $participant = Participant::create($participantData);
            
            // Generate ticket with validity based on eligible days or category
            $eligibleDays = $participantData['eligible_days'] ?? 3;
            $ticket = new Ticket([
                'ticket_number' => Ticket::generateTicketNumber(),
                'registered_by_user_id' => Auth::id(),
                'day1_valid' => $eligibleDays >= 1,
                'day2_valid' => $eligibleDays >= 2,
                'day3_valid' => $eligibleDays >= 3,
                'active' => true
            ]);
            
            // Calculate and set expiration date
            $ticket->expiration_date = Ticket::calculateExpirationDate(
                $eligibleDays >= 1,
                $eligibleDays >= 2,
                $eligibleDays >= 3
            );
            
            $participant->ticket()->save($ticket);
            
            DB::commit();
            
            // Clear registration data from session
            Session::forget('registration_data');
            
            return redirect()->route('usher.registration.ticket', ['ticket' => $ticket->id])
                ->with('success', 'Registration completed successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration error: ' . $e->getMessage());
            return redirect()->route('usher.registration.step1')
                ->with('error', 'An error occurred during registration. Please try again.');
        }
    }
    
    /**
     * Get roles for a specific category via AJAX
     */
    public function getRolesByCategory(Request $request)
    {
        $category = $request->input('category');
        
        $roles = $this->getCategoryRoles($category);
        
        return response()->json(['roles' => $roles]);
    }
    
    /**
     * Show generated ticket
     */
    public function showTicket($ticketId)
    {
        $ticket = Ticket::with(['participant', 'registeredBy'])->findOrFail($ticketId);
        
        // Only allow the user who registered this participant to view the ticket
        if ($ticket->registered_by_user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'You are not authorized to view this ticket.');
        }
        
        // Get the active conference days
        $conferenceDays = ConferenceDay::where('active', true)
            ->orderBy('date')
            ->get();
            
        return view('usher.registration.ticket', compact('ticket', 'conferenceDays'));
    }
    
    /**
     * Get roles for a specific category
     */
    private function getCategoryRoles($category)
    {
        $roles = [
            'general' => ['Delegate'],
            'exhibitor' => ['Exhibitor'],
            'presenter' => ['Conference Presenter'],
            'invited' => ['Chief Guest', 'Guest', 'Keynote Speaker', 'Panelist'],
            'internal' => ['Staff', 'Student'],
            'coordinators' => ['Secretariat', 'Moderator', 'Rapporteur']
        ];
        
        return $roles[$category] ?? [];
    }
    
    /**
     * List registrations made by the current usher
     */
    public function myRegistrations()
    {
        // Get today's conference day if available
        $today = ConferenceDay::getToday();
        
        // Get active conference days for displaying valid days
        $conferenceDays = ConferenceDay::where('active', true)
            ->orderBy('date')
            ->get();
        
        $participants = Participant::where('registered_by_user_id', Auth::id())
            ->with(['ticket', 'checkIns'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('usher.registration.my-registrations', compact('participants', 'today', 'conferenceDays'));
    }
    
    /**
     * Process the registration form submission
     * Legacy method kept for backward compatibility
     */
    public function store(Request $request)
    {
        return $this->legacyStoreMethod($request);
    }
    
    /**
     * Legacy store method implementation
     * Extracted to allow for code reuse
     */
    private function legacyStoreMethod(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|in:general,exhibitor,presenter,invited,internal,coordinators',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'role' => 'required|string|max:255',
            'attendance_days' => 'required|array|min:1',
            'attendance_days.*' => 'exists:conference_days,id',
        ]);
        
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Create participant
            $participant = Participant::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'job_title' => $validated['job_title'] ?? null,
                'organization' => $validated['organization'] ?? null,
                'role' => $validated['role'],
                'category' => $validated['category'],
                'registered_by_user_id' => Auth::id(),
            ]);
            
            // Generate ticket
            $ticket = new Ticket([
                'ticket_number' => Ticket::generateTicketNumber(),
                'registered_by_user_id' => Auth::id(),
                'day1_valid' => in_array(1, $validated['attendance_days']),
                'day2_valid' => in_array(2, $validated['attendance_days']),
                'day3_valid' => in_array(3, $validated['attendance_days']),
                'active' => true
            ]);
            
            $participant->ticket()->save($ticket);
            
            // Check in participant for today if today is one of the selected days
            $today = ConferenceDay::getToday();
            
            if ($today && in_array($today->id, $validated['attendance_days'])) {
                CheckIn::create([
                    'participant_id' => $participant->id,
                    'conference_day_id' => $today->id,
                    'checked_by_user_id' => Auth::id(),
                    'checked_in_at' => now()
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('usher.registration.ticket', $ticket->id)
                ->with('success', 'Participant registered successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to register participant. ' . $e->getMessage());
        }
    }

    /**
     * Get participant details via AJAX for the details modal
     */
    public function getParticipantDetails($id)
    {
        $participant = Participant::with(['ticket', 'checkIns', 'registeredBy'])
            ->findOrFail($id);
        
        // Check if the user is authorized to view this participant
        if ($participant->registered_by_user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Get active conference days for displaying check-in status
        $conferenceDays = ConferenceDay::where('active', true)
            ->orderBy('date')
            ->get();
            
        // Get today's conference day if available
        $today = ConferenceDay::getToday();
            
        // Format check-in information
        $checkInInfo = [];
        foreach ($conferenceDays as $day) {
            $checkInInfo[$day->id] = [
                'day_name' => $day->name,
                'day_date' => $day->date->format('F j, Y'),
                'is_valid' => ($day->id == 1 && $participant->ticket?->day1_valid) || 
                              ($day->id == 2 && $participant->ticket?->day2_valid) || 
                              ($day->id == 3 && $participant->ticket?->day3_valid),
                'is_checked_in' => $participant->isCheckedInForDay($day->id),
                'checked_in_at' => $participant->checkIns->where('conference_day_id', $day->id)->first()?->checked_in_at?->format('M j, Y g:i A'),
            ];
        }
            
        return response()->json([
            'participant' => $participant,
            'check_in_info' => $checkInInfo,
            'html' => view('usher.registration.partials.participant-details', compact('participant', 'checkInInfo', 'conferenceDays', 'today'))->render()
        ]);
    }

    /**
     * View participant details on a dedicated page
     */
    public function viewParticipant($id)
    {
        $participant = Participant::with(['ticket', 'tickets', 'checkIns', 'registeredBy'])
            ->findOrFail($id);
        
        // Check if the user is authorized to view this participant
        if ($participant->registered_by_user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'You are not authorized to view this participant.');
        }
        
        // Get active conference days for displaying check-in status
        $conferenceDays = ConferenceDay::where('active', true)
            ->orderBy('date')
            ->get();
            
        // Get today's conference day if available
        $today = ConferenceDay::getToday();
            
        // Format check-in information
        $checkInInfo = [];
        foreach ($conferenceDays as $day) {
            $checkInInfo[$day->id] = [
                'day_name' => $day->name,
                'day_date' => $day->date->format('F j, Y'),
                'is_valid' => ($day->id == 1 && $participant->ticket?->day1_valid) || 
                              ($day->id == 2 && $participant->ticket?->day2_valid) || 
                              ($day->id == 3 && $participant->ticket?->day3_valid),
                'is_checked_in' => $participant->isCheckedInForDay($day->id),
                'checked_in_at' => $participant->checkIns->where('conference_day_id', $day->id)->first()?->checked_in_at?->format('M j, Y g:i A'),
            ];
        }
            
        // Get all tickets for this participant
        $ticketHistory = $participant->tickets;
            
        return view('usher.registration.view-participant', compact('participant', 'checkInInfo', 'conferenceDays', 'today', 'ticketHistory'));
    }
} 