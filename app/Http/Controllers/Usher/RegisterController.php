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
            'general' => 'General Participants',
            'invited' => 'Invited Guests & Speakers',
            'internal' => 'Internal Participants',
            'coordinators' => 'Session Coordinators'
        ];
        
        $categoryDescriptions = [
            'general' => 'Delegates, Exhibitors, Conference Presenters',
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
            'category' => 'required|string|in:general,invited,internal,coordinators',
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
        
        // Add payment validation rules for general category
        if ($category === 'general') {
            $validationRules['payment_status'] = 'required|string|in:Not Paid,Paid via Vabu,Paid via M-Pesa,Complimentary';
            $validationRules['payment_confirmed'] = 'nullable|boolean';
            $validationRules['payment_notes'] = 'nullable|string';
        }
        
        // Add validation rules for internal participants based on role
        if ($category === 'internal') {
            $role = $request->input('role');
            
            // Make fields optional instead of required
            $validationRules['student_admission_number'] = 'nullable|string|max:50';
            $validationRules['staff_number'] = 'nullable|string|max:50';
        }
        
        $validated = $request->validate($validationRules);
        
        // Store the form data in the session
        $data = array_merge($data, $validated);
        Session::put('registration_data', $data);
        
        // If general category and payment status is "Not Paid", redirect to payment form
        if ($category === 'general' && $validated['payment_status'] === 'Not Paid') {
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
        // Check if all steps are completed
        $data = Session::get('registration_data', []);
        if (!isset($data['category']) || !isset($data['full_name']) || 
            !isset($data['attendance_days']) || !isset($data['check_in_today'])) {
            return redirect()->route('usher.registration.step1');
        }
        
        try {
            // Begin transaction
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
                    // For general category, use the payment status from the form
                    $participantData['payment_status'] = $data['payment_status'] ?? 'Not Paid';
                    $participantData['payment_confirmed'] = isset($data['payment_confirmed']) && $data['payment_confirmed'] ? true : false;
                    break;
                    
                case 'invited':
                case 'coordinators':
                    // For invited guests/speakers and coordinators, payment is Not Applicable
                    $participantData['payment_status'] = 'Not Applicable';
                    $participantData['payment_confirmed'] = true;
                    break;
                    
                case 'internal':
                    // For internal participants (staff and students), payment is Waived
                    $participantData['payment_status'] = 'Waived';
                    $participantData['payment_confirmed'] = true;
                    break;
            }
            
            $participant = Participant::create($participantData);
            
            // Generate ticket
            $ticket = new Ticket([
                'ticket_number' => Ticket::generateTicketNumber(),
                'registered_by_user_id' => Auth::id(),
                'day1_valid' => in_array(1, $data['attendance_days']),
                'day2_valid' => in_array(2, $data['attendance_days']),
                'day3_valid' => in_array(3, $data['attendance_days']),
                'active' => true
            ]);
            
            // Calculate and set the expiration date for the ticket
            $ticket->expiration_date = Ticket::calculateExpirationDate(
                in_array(1, $data['attendance_days']),
                in_array(2, $data['attendance_days']),
                in_array(3, $data['attendance_days'])
            );
            
            $participant->ticket()->save($ticket);
            
            // Check in participant for today if requested and today is one of the selected days
            if ($data['check_in_today']) {
                $today = ConferenceDay::getToday();
                
                if ($today && in_array($today->id, $data['attendance_days'])) {
                    CheckIn::create([
                        'participant_id' => $participant->id,
                        'conference_day_id' => $today->id,
                        'checked_by_user_id' => Auth::id(),
                        'checked_in_at' => now()
                    ]);
                }
            }
            
            // Send the ticket via email automatically
            try {
                Mail::to($participant->email)->send(new TicketMail($ticket));
                
                Log::info('Ticket email sent automatically after registration', [
                    'participant_id' => $participant->id,
                    'email' => $participant->email,
                    'ticket_number' => $ticket->ticket_number
                ]);
            } catch (\Exception $e) {
                // Log the error but don't fail the registration process
                Log::error('Failed to send ticket email during registration', [
                    'participant_id' => $participant->id,
                    'error' => $e->getMessage()
                ]);
                // We'll still proceed with registration even if email fails
            }
            
            DB::commit();
            
            // Clear the session data
            Session::forget('registration_data');
            
            return redirect()->route('usher.registration.ticket', $ticket->id)
                ->with('success', 'Participant registered successfully. Ticket has been sent to ' . $participant->email);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('usher.registration.step1')
                ->with('error', 'Failed to register participant. ' . $e->getMessage());
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
            'general' => ['Delegate', 'Exhibitor', 'Conference Presenter'],
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
            'category' => 'required|string|in:general,invited,internal,coordinators',
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