<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\CheckIn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exports\ParticipantsExport;

class ParticipantController extends Controller
{
    /**
     * Display the categories overview page with dynamic data
     */
    public function index()
    {
        // Get participant statistics
        $totalParticipants = Participant::count();
        $todayCheckIns = CheckIn::whereDate('created_at', Carbon::today())->count();
        $pendingPayments = Participant::where('payment_status', '!=', 'paid')->count();
        
        // Calculate days remaining (assuming the last conference day is the reference)
        $lastConferenceDay = DB::table('conference_days')->orderBy('date', 'desc')->first();
        $daysRemaining = $lastConferenceDay ? Carbon::parse($lastConferenceDay->date)->diffInDays(Carbon::now()) : 0;
        
        // Get counts for each role type
        $generalCount = Participant::whereIn('role', ['delegate', 'exhibitor', 'presenter'])->count();
        $invitedCount = Participant::whereIn('role', ['guest', 'vip', 'panelist'])->count();
        $internalCount = Participant::whereIn('role', ['staff', 'faculty', 'student'])->count();
        $coordinatorsCount = Participant::whereIn('role', ['organizer', 'coordinator'])->count();
        
        // Calculate percentages
        $generalPercentage = $totalParticipants > 0 ? round(($generalCount / $totalParticipants) * 100) : 0;
        $invitedPercentage = $totalParticipants > 0 ? round(($invitedCount / $totalParticipants) * 100) : 0;
        $internalPercentage = $totalParticipants > 0 ? round(($internalCount / $totalParticipants) * 100) : 0;
        $coordinatorsPercentage = $totalParticipants > 0 ? round(($coordinatorsCount / $totalParticipants) * 100) : 0;
        
        return view('admin.participants.index', compact(
            'totalParticipants', 'todayCheckIns', 'pendingPayments', 'daysRemaining',
            'generalCount', 'invitedCount', 'internalCount', 'coordinatorsCount',
            'generalPercentage', 'invitedPercentage', 'internalPercentage', 'coordinatorsPercentage'
        ));
    }
    
    /**
     * Display the category specific participant list
     */
    public function category($category)
    {
        // Validate category
        $validCategories = ['general', 'invited', 'internal', 'coordinators'];
        
        if (!in_array($category, $validCategories)) {
            abort(404);
        }
        
        // Map categories to roles
        $roleMap = [
            'general' => ['delegate', 'exhibitor', 'presenter'],
            'invited' => ['guest', 'vip', 'panelist'],
            'internal' => ['staff', 'faculty', 'student'],
            'coordinators' => ['organizer', 'coordinator']
        ];
        
        // Get participants for the selected category
        $participants = Participant::whereIn('role', $roleMap[$category])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Get the title for the category
        $titles = [
            'general' => 'General Participants',
            'invited' => 'Invited Guests & Speakers',
            'internal' => 'Internal Participants',
            'coordinators' => 'Session Coordinators'
        ];
        
        $title = $titles[$category];
        $subtitle = $this->getCategoryDescription($category);
        
        return view('admin.participants.category', compact('participants', 'category', 'title', 'subtitle'));
    }
    
    /**
     * Export all participants to Excel
     */
    public function exportAll()
    {
        try {
            $fileName = 'all-participants-' . date('Y-m-d') . '.csv';
            $export = new ParticipantsExport();
            return $export->download($fileName);
        } catch (\Exception $e) {
            \Log::error('Export All Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting participants: ' . $e->getMessage());
        }
    }
    
    /**
     * Export category participants to Excel
     */
    public function exportCategory($category)
    {
        try {
            // Validate category
            $validCategories = ['general', 'invited', 'internal', 'coordinators'];
            
            if (!in_array($category, $validCategories)) {
                abort(404);
            }
            
            // Map categories to roles
            $roleMap = [
                'general' => ['delegate', 'exhibitor', 'presenter'],
                'invited' => ['guest', 'vip', 'panelist'],
                'internal' => ['staff', 'faculty', 'student'],
                'coordinators' => ['organizer', 'coordinator']
            ];
            
            $fileName = $category . '-participants-' . date('Y-m-d') . '.csv';
            $export = new ParticipantsExport($roleMap[$category]);
            return $export->download($fileName);
        } catch (\Exception $e) {
            \Log::error('Export Category Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting ' . $category . ' participants: ' . $e->getMessage());
        }
    }
    
    /**
     * Show role selection form
     */
    public function selectRole($category)
    {
        // Validate category
        $validCategories = ['general', 'invited', 'internal', 'coordinators'];
        
        if (!in_array($category, $validCategories)) {
            abort(404);
        }
        
        return view('admin.participants.role-select', compact('category'));
    }
    
    /**
     * Show form to create a new participant
     */
    public function create($category, Request $request)
    {
        // Validate category
        $validCategories = ['general', 'invited', 'internal', 'coordinators'];
        
        if (!in_array($category, $validCategories)) {
            abort(404);
        }
        
        // Get the role from the request
        $role = $request->query('role');
        
        if (!$role) {
            return redirect()->route('admin.participants.select-role', $category);
        }
        
        // Validate that the role belongs to the category
        $roleMap = [
            'general' => ['delegate', 'exhibitor', 'presenter'],
            'invited' => ['guest', 'vip', 'panelist'],
            'internal' => ['staff', 'faculty', 'student'],
            'coordinators' => ['organizer', 'coordinator']
        ];
        
        if (!in_array($role, $roleMap[$category])) {
            return redirect()->route('admin.participants.select-role', $category)
                ->with('error', 'Invalid role selected for this category.');
        }
        
        $title = $this->getCategoryTitle($category);
        $roles = $this->getCategoryRoles($category);
        
        // Get conference days for eligibility selection
        $conferenceDays = DB::table('conference_days')->orderBy('date')->get();
        
        return view('admin.participants.create', compact('category', 'role', 'title', 'roles', 'conferenceDays'));
    }
    
    /**
     * Get roles available for each category
     */
    private function getCategoryRoles($category)
    {
        $roles = [
            'general' => ['Delegate', 'Exhibitor', 'Conference Presenter'],
            'invited' => ['Chief Guest', 'Guest', 'Keynote Speaker', 'Panelist'],
            'internal' => ['Staff', 'Student'],
            'coordinators' => ['Secretariat', 'Moderator', 'Rapporteur']
        ];
        
        return $roles[$category];
    }
    
    /**
     * Get category title
     */
    private function getCategoryTitle($category)
    {
        $titles = [
            'general' => 'General Participants',
            'invited' => 'Invited Guests & Speakers',
            'internal' => 'Internal Participants',
            'coordinators' => 'Session Coordinators'
        ];
        
        return $titles[$category];
    }
    
    /**
     * Get category description
     */
    private function getCategoryDescription($category)
    {
        $descriptions = [
            'general' => 'Delegates, Exhibitors, Conference Presenters',
            'invited' => 'Chief Guests, Guests, Keynote Speakers, Panelists',
            'internal' => 'Staff, Students',
            'coordinators' => 'Secretariat, Moderators, Rapporteurs'
        ];
        
        return $descriptions[$category];
    }
    
    /**
     * Store a newly created participant
     */
    public function store(Request $request, $category)
    {
        // Validate category
        $validCategories = ['general', 'invited', 'internal', 'coordinators'];
        
        if (!in_array($category, $validCategories)) {
            abort(404);
        }
        
        // Validate the request data
        $rules = [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'role' => 'required|string',
            'organization' => 'nullable|string|max:255',
            'payment_status' => 'required|in:Not Paid,Paid via M-Pesa,Paid via Vabu,Waived',
            'payment_amount' => 'nullable|numeric',
            'eligible_days' => 'required|string'
        ];
        
        // Add conditional validation rules based on role
        if ($request->role == 'student') {
            $rules['student_admission_number'] = 'required|string|max:50';
        }
        
        if ($request->role == 'staff') {
            $rules['staff_number'] = 'required|string|max:50';
        }
        
        if ($request->role == 'presenter') {
            $rules['presenter_type'] = 'required|in:student,non_student,international';
        }
        
        $validatedData = $request->validate($rules);
        
        // Create the new participant
        $participant = new Participant();
        $participant->full_name = $validatedData['full_name'];
        $participant->email = $validatedData['email'];
        $participant->phone_number = $validatedData['phone_number'];
        $participant->role = $validatedData['role'];
        $participant->organization = $validatedData['organization'] ?? null;
        $participant->payment_status = $validatedData['payment_status'];
        $participant->payment_amount = $validatedData['payment_amount'] ?? 0;
        $participant->eligible_days = $validatedData['eligible_days'];
        $participant->category = $category;
        
        // Add conditional fields based on role
        if ($request->role == 'student') {
            $participant->student_admission_number = $validatedData['student_admission_number'];
        }
        
        if ($request->role == 'staff') {
            $participant->staff_number = $validatedData['staff_number'];
        }
        
        if ($request->role == 'presenter') {
            $participant->presenter_type = $validatedData['presenter_type'];
        }
        
        // Set payment confirmation
        $participant->payment_confirmed = $request->has('payment_confirmed');
        
        // Set registered by (the admin user)
        $participant->registered_by_user_id = auth()->id();
        
        // Save the participant
        $participant->save();
        
        return redirect()->route('admin.participants.category', $category)
            ->with('success', 'Participant added successfully!');
    }
    
    /**
     * Show participant details
     */
    public function show($category, $id)
    {
        // Validate category
        $validCategories = ['general', 'invited', 'internal', 'coordinators'];
        
        if (!in_array($category, $validCategories)) {
            abort(404);
        }
        
        // Get the participant
        $participant = Participant::findOrFail($id);
        
        // Check if the participant belongs to the selected category
        $roleMap = [
            'general' => ['delegate', 'exhibitor', 'presenter'],
            'invited' => ['guest', 'vip', 'panelist'],
            'internal' => ['staff', 'faculty', 'student'],
            'coordinators' => ['organizer', 'coordinator']
        ];
        
        if (!in_array($participant->role, $roleMap[$category])) {
            abort(404);
        }
        
        $title = $this->getCategoryTitle($category);
        
        return view('admin.participants.show', compact('category', 'participant', 'title'));
    }
    
    /**
     * Show form to edit participant
     */
    public function edit($category, $id)
    {
        // Validate category
        $validCategories = ['general', 'invited', 'internal', 'coordinators'];
        
        if (!in_array($category, $validCategories)) {
            abort(404);
        }
        
        // Get the participant
        $participant = Participant::findOrFail($id);
        
        // Check if the participant belongs to the selected category
        $roleMap = [
            'general' => ['delegate', 'exhibitor', 'presenter'],
            'invited' => ['guest', 'vip', 'panelist'],
            'internal' => ['staff', 'faculty', 'student'],
            'coordinators' => ['organizer', 'coordinator']
        ];
        
        if (!in_array($participant->role, $roleMap[$category])) {
            abort(404);
        }
        
        $title = $this->getCategoryTitle($category);
        $roles = $this->getCategoryRoles($category);
        $role = $participant->role;
        
        // Get conference days for eligibility selection
        $conferenceDays = DB::table('conference_days')->orderBy('date')->get();
        
        return view('admin.participants.edit', compact('category', 'participant', 'title', 'roles', 'role', 'conferenceDays'));
    }
    
    /**
     * Update the specified participant
     */
    public function update(Request $request, $category, $id)
    {
        // Validate category
        $validCategories = ['general', 'invited', 'internal', 'coordinators'];
        
        if (!in_array($category, $validCategories)) {
            abort(404);
        }
        
        // Get the participant
        $participant = Participant::findOrFail($id);
        
        // Validate the request data
        $rules = [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'role' => 'required|string',
            'organization' => 'nullable|string|max:255',
            'payment_status' => 'required|in:Not Paid,Paid via M-Pesa,Paid via Vabu,Waived',
            'payment_amount' => 'nullable|numeric',
            'eligible_days' => 'required|string'
        ];
        
        // Add conditional validation rules based on role
        if ($request->role == 'student') {
            $rules['student_admission_number'] = 'required|string|max:50';
        }
        
        if ($request->role == 'staff') {
            $rules['staff_number'] = 'required|string|max:50';
        }
        
        if ($request->role == 'presenter') {
            $rules['presenter_type'] = 'required|in:student,non_student,international';
        }
        
        $validatedData = $request->validate($rules);
        
        // Update the participant
        $participant->full_name = $validatedData['full_name'];
        $participant->email = $validatedData['email'];
        $participant->phone_number = $validatedData['phone_number'];
        $participant->role = $validatedData['role'];
        $participant->organization = $validatedData['organization'] ?? null;
        $participant->payment_status = $validatedData['payment_status'];
        $participant->payment_amount = $validatedData['payment_amount'] ?? 0;
        $participant->eligible_days = $validatedData['eligible_days'];
        
        // Add conditional fields based on role
        if ($request->role == 'student') {
            $participant->student_admission_number = $validatedData['student_admission_number'];
        }
        
        if ($request->role == 'staff') {
            $participant->staff_number = $validatedData['staff_number'];
        }
        
        if ($request->role == 'presenter') {
            $participant->presenter_type = $validatedData['presenter_type'];
        }
        
        // Set payment confirmation
        $participant->payment_confirmed = $request->has('payment_confirmed');
        
        // Save the participant
        $participant->save();
        
        return redirect()->route('admin.participants.category', $category)
            ->with('success', 'Participant updated successfully!');
    }
    
    /**
     * Remove the specified participant
     */
    public function destroy($category, $id)
    {
        // Validate category
        $validCategories = ['general', 'invited', 'internal', 'coordinators'];
        
        if (!in_array($category, $validCategories)) {
            abort(404);
        }
        
        // Get the participant
        $participant = Participant::findOrFail($id);
        
        // Delete the participant
        $participant->delete();
        
        return redirect()->route('admin.participants.category', $category)
            ->with('success', 'Participant deleted successfully!');
    }
    
    /**
     * Show form to import participants
     */
    public function showImport($category)
    {
        $title = $this->getCategoryTitle($category);
        return view('admin.participants.import', compact('category', 'title'));
    }
} 