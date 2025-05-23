<?php

namespace App\Http\Controllers\Usher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MealType;
use App\Models\MealServing;
use App\Models\Participant;
use App\Models\Ticket;
use App\Models\ConferenceDay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MealController extends Controller
{
    /**
     * Display the meal serving page.
     */
    public function index(Request $request)
    {
        // Get all active meal types
        $mealTypes = MealType::where('active', true)
            ->orderBy('start_time')
            ->get();
            
        // Get today's conference day
        $today = ConferenceDay::getToday();
        
        // Get meal statistics for today
        $stats = [];
        if ($today) {
            foreach ($mealTypes as $mealType) {
                $stats[$mealType->id] = [
                    'name' => $mealType->name,
                    'count' => MealServing::where('meal_type_id', $mealType->id)
                        ->where('conference_day_id', $today->id)
                        ->count(),
                    'is_current' => $mealType->isCurrentlyServed()
                ];
            }
        }
        
        // Get the selected meal type from session or request
        $selectedMealTypeId = session('selected_meal_type_id', $request->input('meal_type_id'));
        $selectedMealType = null;
        
        // If no meal type is selected but there's a current meal being served, select that one
        if (!$selectedMealTypeId && $mealTypes->isNotEmpty()) {
            // First try to find a meal type that is currently being served
            foreach ($mealTypes as $mealType) {
                if (isset($stats[$mealType->id]) && $stats[$mealType->id]['is_current']) {
                    $selectedMealTypeId = $mealType->id;
                    $selectedMealType = $mealType;
                    break;
                }
            }
            
            // If no current meal type, just select the first one
            if (!$selectedMealTypeId) {
                $selectedMealTypeId = $mealTypes->first()->id;
                $selectedMealType = $mealTypes->first();
            }
        } elseif ($selectedMealTypeId) {
            // Load the selected meal type
            $selectedMealType = MealType::find($selectedMealTypeId);
        }
        
        // Store the selected meal type in session
        if ($selectedMealTypeId) {
            session(['selected_meal_type_id' => $selectedMealTypeId]);
        }
        
        return view('usher.meals.index', compact(
            'mealTypes', 
            'today', 
            'stats', 
            'selectedMealTypeId', 
            'selectedMealType'
        ));
    }
    
    /**
     * Select a meal type.
     */
    public function selectMeal(Request $request)
    {
        $mealTypeId = $request->input('meal_type_id');
        
        if ($mealTypeId) {
            // Store the selected meal type in session
            session(['selected_meal_type_id' => $mealTypeId]);
            
            if ($request->ajax()) {
                // If it's an AJAX request, return JSON response
                return response()->json([
                    'success' => true,
                    'meal_type_id' => $mealTypeId
                ]);
            }
        }
        
        return redirect()->route('usher.meals', ['meal_type_id' => $mealTypeId]);
    }
    
    /**
     * Process a meal serving.
     */
    public function serve(Request $request)
    {
        $validated = $request->validate([
            'meal_type_id' => 'required|exists:meal_types,id',
            'ticket_number' => 'required|string|max:4',
            'notes' => 'nullable|string',
        ]);
        
        // Store the meal type ID in session for persistence
        session(['selected_meal_type_id' => $validated['meal_type_id']]);
        
        // Get today's conference day
        $today = ConferenceDay::getToday();
        
        if (!$today) {
            return redirect()->back()->with('error', 'There is no active conference day today.');
        }
        
        // Add the standard prefix to the ticket number
        $fullTicketNumber = 'ZU-RIW25-' . $validated['ticket_number'];
        
        // Look up the ticket
        $ticket = Ticket::where('ticket_number', $fullTicketNumber)
            ->where('active', true)
            ->first();
            
        if (!$ticket) {
            return redirect()->back()
                ->with('error', 'Invalid ticket number or ticket not active.');
        }
        
        // Check if the ticket is valid for today
        $dayField = 'day' . $today->id . '_valid';
        if (!$ticket->$dayField) {
            return redirect()->back()
                ->with('error', 'This ticket is not valid for today.');
        }
        
        // Check if the ticket is expired
        if ($ticket->isExpired()) {
            return redirect()->back()
                ->with('error', 'This ticket has expired.');
        }
        
        // Get the participant
        $participant = $ticket->participant;
        
        // Check if participant has already been served this meal today
        if ($participant->hasBeenServedMeal($validated['meal_type_id'], $today->id)) {
            return redirect()->back()
                ->with('error', 'This participant has already been served this meal today.');
        }
        
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Create meal serving record
            MealServing::create([
                'participant_id' => $participant->id,
                'meal_type_id' => $validated['meal_type_id'],
                'conference_day_id' => $today->id,
                'served_by_user_id' => Auth::id(),
                'ticket_number' => $fullTicketNumber,
                'served_at' => now(),
                'notes' => $validated['notes'] ?? null
            ]);
            
            DB::commit();
            
            // Get meal name for the success message
            $mealName = MealType::find($validated['meal_type_id'])->name;
            
            return redirect()->route('usher.meals')
                ->with('success', "{$participant->full_name} has been served {$mealName} successfully.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to record meal serving: ' . $e->getMessage());
        }
    }
    
    /**
     * Show statistics for meal servings.
     */
    public function stats(Request $request)
    {
        // Get the selected day or default to today
        $dayId = $request->input('day_id');
        $day = null;
        
        if ($dayId) {
            $day = ConferenceDay::find($dayId);
        } else {
            $day = ConferenceDay::getToday();
        }
        
        // Get all active conference days for the dropdown
        $days = ConferenceDay::where('active', true)
            ->orderBy('date')
            ->get();
            
        // If no day is selected/available
        if (!$day) {
            return view('usher.meals.stats', compact('days', 'day'));
        }
        
        // Get all meal types
        $mealTypes = MealType::where('active', true)->get();
        
        // Calculate statistics
        $stats = [];
        foreach ($mealTypes as $mealType) {
            $servings = MealServing::where('meal_type_id', $mealType->id)
                ->where('conference_day_id', $day->id)
                ->count();
                
            $stats[$mealType->id] = [
                'name' => $mealType->name,
                'count' => $servings,
                'is_current' => $mealType->isCurrentlyServed()
            ];
        }
        
        // Get recent servings
        $recentServings = MealServing::with(['participant', 'mealType', 'servedBy'])
            ->where('conference_day_id', $day->id)
            ->orderBy('served_at', 'desc')
            ->limit(20)
            ->get();
            
        return view('usher.meals.stats', compact('day', 'days', 'stats', 'mealTypes', 'recentServings'));
    }
    
    /**
     * Search participants who have been served a meal.
     */
    public function searchParticipants(Request $request)
    {
        $query = $request->input('query');
        $mealTypeId = $request->input('meal_type_id');
        $dayId = $request->input('day_id');
        
        if (empty($query) || strlen($query) < 3) {
            return response()->json([
                'participants' => []
            ]);
        }
        
        // Get participants who match the search query
        $participants = Participant::where('email', 'like', "%{$query}%")
            ->orWhere('full_name', 'like', "%{$query}%")
            ->orWhere('phone_number', 'like', "%{$query}%")
            ->limit(10)
            ->get();
            
        foreach ($participants as $participant) {
            $participant->has_been_served = $participant->hasBeenServedMeal($mealTypeId, $dayId);
            $participant->ticket = $participant->ticket;
        }
        
        return response()->json([
            'participants' => $participants
        ]);
    }
}
