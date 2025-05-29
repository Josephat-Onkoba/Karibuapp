<?php

namespace App\Http\Controllers\Usher;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\ConferenceDay;
use App\Models\Participant;
use App\Models\Payment;
use App\Models\Ticket;
use App\Services\TalkSasaSmsService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RegisterController extends Controller
{
    protected $smsService;

    public function __construct(TalkSasaSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

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
     * Validate payment status for categories that require payment
     * @param array $data Registration data from session
     * @return bool|string Returns true if valid, or redirect route if invalid
     */
    private function validatePaymentStatus($data)
    {
        // Only applicable for categories that require payment
        $paidCategories = ['general', 'exhibitor', 'presenter'];
        
        // Check if the category requires payment
        if (!in_array($data['category'], $paidCategories)) {
            return true;
        }
        
        // If payment is waived or not applicable, it's valid
        if (($data['payment_status'] ?? '') === 'Waived' || 
            ($data['payment_status'] ?? '') === 'Not Applicable') {
            return true;
        }
        
        // Check if payment status is set
        if (!isset($data['payment_status'])) {
            return 'usher.registration.step2';
        }
        
        // If payment status is 'Not Paid', redirect to payment form
        if ($data['payment_status'] === 'Not Paid') {
            return 'usher.registration.payment';
        }
        
        // For paid status, ensure payment is confirmed
        if (in_array($data['payment_status'], ['Paid via Vabu', 'Paid via M-Pesa']) && 
            (!isset($data['payment_confirmed']) || $data['payment_confirmed'] !== true)) {
            return 'usher.registration.payment';
        }
        
        return true;
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
        try {
            // Validate input
            $request->validate([
                'email' => 'required|email',
                'full_name' => 'required|string'
            ]);
            
            // Log the check for debugging
            Log::info('Checking for existing participant', [
                'email' => $request->email,
                'full_name' => $request->full_name
            ]);

            // Search for existing participant
            $participant = Participant::where('email', $request->email)
                ->orWhere('full_name', $request->full_name)
                ->with(['ticket' => function($query) {
                    $query->where('active', true);
                }])
                ->first();

            if ($participant) {
                // Log that we found a match
                Log::info('Found existing participant', ['participant_id' => $participant->id]);
                
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
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error checking for existing participant', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return error response
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while checking for existing participants.'
            ], 500);
        }
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
                $validationRules['payment_status'] = 'required|string|in:Not Paid,Paid via Vabu,Paid via M-Pesa,Waived';
                break;
                
            default:
                // For other categories (invited, internal, coordinators)
                $validationRules['payment_status'] = 'nullable|string';
                break;
        }
        
        // Check for duplicate participants before proceeding
        $existingParticipant = Participant::where('email', $request->email)
            ->orWhere('full_name', $request->full_name)
            ->with(['ticket' => function($query) {
                $query->where('active', true);
            }])
            ->first();
            
        if ($existingParticipant) {
            // Store duplicate info in flash session
            session()->flash('duplicate_warning', true);
            session()->flash('duplicate_participant', [
                'id' => $existingParticipant->id,
                'full_name' => $existingParticipant->full_name,
                'email' => $existingParticipant->email,
                'has_active_ticket' => $existingParticipant->ticket !== null
            ]);
            
            // Log the duplicate
            Log::info('Duplicate participant detected during registration', [
                'existing_id' => $existingParticipant->id,
                'email' => $request->email,
                'full_name' => $request->full_name
            ]);
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
                // Auto-confirm payment if status is Paid
                if (in_array($validated['payment_status'], ['Paid via Vabu', 'Paid via M-Pesa'])) {
                    $validated['payment_confirmed'] = true;
                }
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
                // Auto-confirm payment if status is Paid or Waived
                if (in_array($validated['payment_status'], ['Paid via Vabu', 'Paid via M-Pesa', 'Waived'])) {
                    $validated['payment_confirmed'] = true;
                }
                break;
                
            case 'general':
                if ($validated['payment_status'] === 'Waived') {
                    $validated['payment_confirmed'] = true;
                    $validated['eligible_days'] = 3;
                } else {
                    // Auto-confirm payment if status is Paid
                    if (in_array($validated['payment_status'], ['Paid via Vabu', 'Paid via M-Pesa'])) {
                        $validated['payment_confirmed'] = true;
                    }
                    
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
        // Debug: Log session data at the start of showStep3
        Log::info('RegisterController@showStep3 - Initial session data:', [
            'session_data' => Session::get('registration_data', []),
            'session_id' => Session::getId()
        ]);
        
        // Check if previous steps are completed
        $data = Session::get('registration_data', []);
        if (!isset($data['full_name']) || !isset($data['category'])) {
            Log::warning('RegisterController@showStep3 - Missing required fields, redirecting to step1');
            return redirect()->route('usher.registration.step1');
        }
        
        // For paid categories, ensure payment is confirmed
        if (in_array($data['category'], ['general', 'exhibitor', 'presenter'])) {
            Log::info('RegisterController@showStep3 - Processing paid category:', [
                'category' => $data['category'],
                'payment_status' => $data['payment_status'] ?? 'not set',
                'payment_confirmed' => $data['payment_confirmed'] ?? 'not set',
                'session_id' => Session::getId()
            ]);
            
            // If payment status is 'Not Paid' and not confirmed
            if (($data['payment_status'] ?? '') === 'Not Paid' && !($data['payment_confirmed'] ?? false)) {
                Log::warning('RegisterController@showStep3 - Payment not confirmed, redirecting to payment', [
                    'payment_status' => $data['payment_status'],
                    'payment_confirmed' => $data['payment_confirmed'] ?? 'not set',
                    'session_data' => $data
                ]);
                return redirect()->route('usher.registration.payment');
            }
            
            // If payment status is paid but not confirmed
            if (in_array(($data['payment_status'] ?? ''), ['Paid via Vabu', 'Paid via M-Pesa']) && 
                !($data['payment_confirmed'] ?? false)) {
                Log::warning('RegisterController@showStep3 - Paid but not confirmed, redirecting to payment', [
                    'payment_status' => $data['payment_status'],
                    'payment_confirmed' => $data['payment_confirmed'] ?? 'not set',
                    'session_data' => $data
                ]);
                return redirect()->route('usher.registration.payment');
            }
            
            // If we have a payment status but no confirmation, try to confirm it
            if (isset($data['payment_status']) && !isset($data['payment_confirmed'])) {
                $data['payment_confirmed'] = true;
                Session::put('registration_data', $data);
                Session::save();
                Log::info('RegisterController@showStep3 - Auto-confirmed payment', [
                    'payment_status' => $data['payment_status'],
                    'session_id' => Session::getId()
                ]);
            }
            
            Log::info('RegisterController@showStep3 - Payment validation passed', [
                'payment_status' => $data['payment_status'] ?? 'not set',
                'payment_confirmed' => $data['payment_confirmed'] ?? 'not set'
            ]);
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
        
        // Get registration data
        $data = Session::get('registration_data', []);
        
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
        
        // For general category with paid status, validate number of days selected
        if ($data['category'] === 'general' && 
            isset($data['payment_status']) && 
            $data['payment_status'] !== 'Waived' && 
            isset($data['eligible_days'])) {
            
            $selectedDaysCount = count($validated['attendance_days']);
            $eligibleDays = (int)$data['eligible_days'];
            
            if ($selectedDaysCount > $eligibleDays) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['attendance_days' => "You can only select {$eligibleDays} days based on your payment."]);
            }
            
            if ($selectedDaysCount < $eligibleDays) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['attendance_days' => "Please select all {$eligibleDays} days that you paid for."]);
            }
        }
        
        // Store the form data in the session
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
        if (!isset($data['full_name']) || !isset($data['category'])) {
            return redirect()->route('usher.registration.step1');
        }
        
        if (!isset($data['attendance_days'])) {
            return redirect()->route('usher.registration.step3');
        }
        
        // Validate payment status
        $paymentValidation = $this->validatePaymentStatus($data);
        if ($paymentValidation !== true) {
            return redirect()->route($paymentValidation);
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
        if (!isset($data['full_name']) || !isset($data['category'])) {
            return redirect()->route('usher.registration.step1');
        }
        
        if (!isset($data['attendance_days'])) {
            return redirect()->route('usher.registration.step3');
        }
        
        // Validate payment status
        $paymentValidation = $this->validatePaymentStatus($data);
        if ($paymentValidation !== true) {
            return redirect()->route($paymentValidation);
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
            return redirect()->route('usher.registration.step1')
                ->with('error', 'Registration data is incomplete. Please start from the beginning.');
        }
        
        try {
            DB::beginTransaction();
            
            // Create participant with all required fields
            $participantData = [
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'],
                'job_title' => $data['job_title'] ?? null,
                'organization' => $data['organization'] ?? null,
                'role' => $data['role'],
                'category' => $data['category'],
                'registered_by_user_id' => Auth::id() // Ensure this required field is set
            ];
            
            // Add category-specific fields
            switch ($data['category']) {
                case 'general':
                    $participantData['payment_status'] = $data['payment_status'] ?? 'Not Paid';
                    $participantData['payment_confirmed'] = $data['payment_confirmed'] ?? false;
                    $participantData['payment_amount'] = $data['payment_amount'] ?? 0.00;
                    $participantData['eligible_days'] = $data['eligible_days'] ?? 1;
                    break;
                
                case 'exhibitor':
                    $participantData['payment_status'] = $data['payment_status'] ?? 'Not Paid';
                    $participantData['payment_confirmed'] = $data['payment_confirmed'] ?? false;
                    $participantData['payment_amount'] = $data['payment_amount'] ?? Participant::EXHIBITOR_FEE;
                    $participantData['eligible_days'] = 3; // Full conference period
                    break;
                
                case 'presenter':
                    $participantData['presenter_type'] = $data['presenter_type'] ?? null;
                    $participantData['payment_status'] = $data['payment_status'] ?? 'Not Paid';
                    $participantData['payment_confirmed'] = $data['payment_confirmed'] ?? false;
                    $participantData['payment_amount'] = $data['payment_amount'] ?? 0.00;
                    $participantData['eligible_days'] = 3; // Full conference period
                    break;
                
                case 'invited': 
                case 'coordinators':
                    $participantData['payment_status'] = 'Not Applicable';
                    $participantData['payment_confirmed'] = true;
                    $participantData['eligible_days'] = 3; // Full conference period
                    break;
                
                case 'internal':
                    $participantData['student_admission_number'] = $data['student_admission_number'] ?? null;
                    $participantData['staff_number'] = $data['staff_number'] ?? null;
                    $participantData['payment_status'] = 'Waived';
                    $participantData['payment_confirmed'] = true;
                    $participantData['eligible_days'] = 3; // Full conference period
                    break;
                
                default:
                    // Handle unexpected category
                    Log::warning('Unknown participant category encountered: ' . $data['category']);
                    $participantData['payment_status'] = 'Not Paid';
                    $participantData['payment_confirmed'] = false;
                    $participantData['eligible_days'] = 1;
                    break;
            }
            
            // Create participant
            $participant = Participant::create($participantData);
            
            // Create payment record if payment was made
            if (isset($participantData['payment_confirmed']) && $participantData['payment_confirmed'] && 
                in_array($participantData['category'], ['general', 'exhibitor', 'presenter']) && 
                isset($participantData['payment_amount']) && $participantData['payment_amount'] > 0) {
                
                // Determine payment method from payment status
                $paymentMethod = 'Other';
                $transactionCode = null;
                $paymentNotes = $data['payment_notes'] ?? '';
                
                if (isset($data['payment_status'])) {
                    if (strpos($data['payment_status'], 'M-Pesa') !== false) {
                        $paymentMethod = 'mpesa';
                        $transactionCode = $data['transaction_code'] ?? null;
                        $paymentNotes = 'M-Pesa Transaction: ' . $transactionCode . "\n" . $paymentNotes;
                    } elseif (strpos($data['payment_status'], 'Vabu') !== false) {
                        $paymentMethod = 'vabu';
                        $transactionCode = $data['transaction_code'] ?? ('VABU-' . time());
                        $paymentNotes = 'Vabu Transaction: ' . $transactionCode . "\n" . $paymentNotes;
                    }
                }
                
                // Ensure we have a valid transaction code
                if (empty($transactionCode)) {
                    $transactionCode = 'AUTO-' . Str::random(8) . '-' . time();
                    $paymentNotes = 'Auto-generated transaction reference: ' . $transactionCode . "\n" . $paymentNotes;
                    Log::info('Generated transaction code for payment', [
                        'participant_id' => $participant->id,
                        'category' => $participant->category,
                        'transaction_code' => $transactionCode
                    ]);
                }
                
                try {
                    // Create payment record with all required fields
                    $payment = Payment::create([
                        'participant_id' => $participant->id,
                        'amount' => $participantData['payment_amount'],
                        'payment_method' => $paymentMethod,
                        'transaction_code' => $transactionCode,
                        'notes' => $paymentNotes,
                        'processed_by_user_id' => $data['processed_by_user_id'] ?? Auth::id(),
                        'payment_confirmed' => true,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                    
                    Log::info('Payment record created successfully', [
                        'payment_id' => $payment->id,
                        'participant_id' => $participant->id,
                        'amount' => $payment->amount,
                        'method' => $payment->payment_method
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create payment record', [
                        'error' => $e->getMessage(),
                        'participant_id' => $participant->id,
                        'transaction_code' => $transactionCode
                    ]);
                    // Continue processing - don't fail the entire registration because of payment record creation issue
                }
            }
            
            // Generate ticket with validity based on eligible days
            $eligibleDays = $participantData['eligible_days'];
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
            
            // Create check-in record if check_in_today is true and there's an active conference day
            // Convert the string value '1' to boolean true for comparison
            if (isset($data['check_in_today']) && ($data['check_in_today'] === true || $data['check_in_today'] === '1')) {
                // Get today's conference day
                $today = ConferenceDay::getToday();
                
                if ($today) {
                    // Check if the participant can be checked in for today
                    $canCheckIn = in_array($today->id, $data['attendance_days'] ?? []);
                    
                    if ($canCheckIn) {
                        // Ensure payment_confirmed attribute is set properly on the participant model
                        // This is critical for the payment eligibility check
                        if (isset($data['payment_status']) && 
                            ($data['payment_status'] === 'Paid via Vabu' || 
                             $data['payment_status'] === 'Paid via M-Pesa' || 
                             $data['payment_status'] === 'Waived' ||
                             in_array($participant->category, ['invited', 'internal', 'coordinators']))) {
                                 
                            // Update participant directly to ensure payment_confirmed is set
                            $participant->payment_confirmed = true;
                            $participant->save();
                        }
                        
                        // Check payment eligibility using the same logic as CheckInController
                        $paymentEligibility = $this->checkPaymentEligibility($participant, $today);
                        
                        if ($paymentEligibility['eligible']) {
                            // Check if already checked in (shouldn't happen, but just to be safe)
                            $alreadyCheckedIn = CheckIn::where('participant_id', $participant->id)
                                ->where('conference_day_id', $today->id)
                                ->exists();
                                
                            if (!$alreadyCheckedIn) {
                                // Use try-catch to ensure we log any check-in failures
                                try {
                                    // Create the check-in record
                                    $checkIn = new CheckIn([
                                        'participant_id' => $participant->id,
                                        'conference_day_id' => $today->id,
                                        'checked_by_user_id' => Auth::id(),
                                        'checked_in_at' => now(),
                                        'notes' => 'Created during registration'
                                    ]);
                                    
                                    $checkIn->save();
                                    
                                    Log::info('Participant checked in during registration', [
                                        'participant_id' => $participant->id,
                                        'conference_day_id' => $today->id,
                                        'check_in_id' => $checkIn->id
                                    ]);
                                } catch (\Exception $e) {
                                    Log::error('Failed to check in participant during registration', [
                                        'participant_id' => $participant->id,
                                        'conference_day_id' => $today->id,
                                        'error' => $e->getMessage()
                                    ]);
                                }
                            }
                        } else {
                            // Log the eligibility issue but continue registration without check-in
                            Log::warning('Participant not eligible for check-in during registration', [
                                'participant_id' => $participant->id,
                                'conference_day_id' => $today->id,
                                'reason' => $paymentEligibility['message'],
                                'payment_confirmed' => $participant->payment_confirmed,
                                'payment_status' => $participant->payment_status
                            ]);
                        }
                    }
                }
            }
            
            // Prepare SMS message
            $validDays = [];
            if ($ticket->day1_valid) $validDays[] = "Day 1";
            if ($ticket->day2_valid) $validDays[] = "Day 2";
            if ($ticket->day3_valid) $validDays[] = "Day 3";
            
            $message = "Dear {$participant->full_name}, welcome to the 7th Zetech University Research Conference!\n\n";
            $message .= "Your registration is complete.\n";
            $message .= "Access Ticket #: {$ticket->ticket_number}\n";
            $message .= "Category: {$participant->category}\n";
            $message .= "Valid for: " . implode(", ", $validDays) . "\n\n";
            
            // Add payment information if applicable
                if ($participant->payment_status === 'Not Paid') {
                    $message .= "Payment Required: KES " . number_format($participant->payment_amount, 2) . "\n";
                    $message .= "Pay via:\n";
                    $message .= "M-Pesa Paybill: 303030\n";
                    $message .= "Account: 2031653161\n";
                    $message .= "Please complete payment to attend.";
                } elseif ($participant->payment_status === 'Paid via M-Pesa' || $participant->payment_status === 'Paid via Vabu') {
                    $message .= "Welcome to Zetech University. ";
                    $message .= "For more information about the conference, visit https://conference.zetech.ac.ke. You can also view the full program at https://conference.zetech.ac.ke/index.php/conference-2025/program";
                }
       
            // Send SMS with retry logic
            $retryCount = 0;
            $maxRetries = 3;
            $smsSuccess = false;
            
            while ($retryCount < $maxRetries && !$smsSuccess) {
                try {
                    $smsResult = $this->smsService->sendSms(
                        $participant->phone_number,
                        $message
                    );
                    
                    if ($smsResult['success']) {
                        $smsSuccess = true;
                    } else {
                        $retryCount++;
                        if ($retryCount < $maxRetries) {
                            sleep(1); // Wait 1 second before retrying
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('SMS sending failed attempt ' . $retryCount, [
                        'error' => $e->getMessage(),
                        'participant_id' => $participant->id
                    ]);
                    $retryCount++;
                    if ($retryCount < $maxRetries) {
                        sleep(1);
                    }
                }
            }
            
            // Log registration completion
            Log::info('Registration completed', [
                'participant_id' => $participant->id,
                'ticket_number' => $ticket->ticket_number,
                'category' => $participant->category,
                'payment_status' => $participant->payment_status,
                'sms_success' => $smsSuccess,
                'sms_retries' => $retryCount,
                'email_sent' => $emailSent ?? false
            ]);
            
            DB::commit();
            
            // Clear registration data from session
            Session::forget('registration_data');
            
            $successMessage = 'Registration completed successfully!';
            $notifications = [];
            
            if (!$smsSuccess) {
                $notifications[] = 'SMS notification could not be sent';
            }
            
            if (!($emailSent ?? false)) {
                $notifications[] = 'Email notification could not be sent';
            }
            
            if (!empty($notifications)) {
                $successMessage .= ' However, ' . implode(' and ', $notifications) . '.';
            }
            
            return redirect()->route('usher.registration.ticket', ['ticket' => $ticket->id])
                ->with('success', $successMessage);
                
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
     * Check payment eligibility for a specific day
     * Same implementation as in CheckInController for consistency
     */
    private function checkPaymentEligibility(Participant $participant, ConferenceDay $conferenceDay): array
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
        // Allow access if user is admin, registered the participant, or has checked in the participant
        $hasCheckedInParticipant = CheckIn::where('participant_id', $participant->id)
            ->where('checked_by_user_id', Auth::id())
            ->exists();
            
        if ($participant->registered_by_user_id !== Auth::id() && Auth::user()->role !== 'admin' && !$hasCheckedInParticipant) {
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