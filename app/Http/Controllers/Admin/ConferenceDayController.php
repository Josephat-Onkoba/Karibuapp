<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConferenceDay;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConferenceDayController extends Controller
{
    /**
     * Display a listing of all conference days.
     */
    public function index()
    {
        $days = ConferenceDay::orderBy('date')->get();
        
        return view('admin.conference-days.index', compact('days'));
    }
    
    /**
     * Show the form for creating a new conference day.
     */
    public function create()
    {
        return view('admin.conference-days.create');
    }
    
    /**
     * Store a newly created conference day.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:conference_days,date',
            'description' => 'nullable|string'
        ]);
        
        try {
            // Begin transaction to ensure all operations complete or none
            DB::beginTransaction();
            
            // Create the conference day
            $day = ConferenceDay::create($validated);
            
            DB::commit();
            
            return redirect()->route('admin.conference-days.index')
                             ->with('success', 'Conference day added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            throw ValidationException::withMessages([
                'general' => ['Failed to create conference day. Please try again.']
            ]);
        }
    }
    
    /**
     * Show the form for editing a conference day.
     */
    public function edit($id)
    {
        $day = ConferenceDay::findOrFail($id);
        
        return view('admin.conference-days.edit', compact('day'));
    }
    
    /**
     * Update the specified conference day.
     */
    public function update(Request $request, $id)
    {
        $day = ConferenceDay::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => "required|date|unique:conference_days,date,{$id}",
            'description' => 'nullable|string'
        ]);
        
        $day->update($validated);
        
        return redirect()->route('admin.conference-days.index')
                         ->with('success', 'Conference day updated successfully.');
    }
    
    /**
     * Remove the specified conference day.
     */
    public function destroy($id)
    {
        $day = ConferenceDay::findOrFail($id);
        
        try {
            // Begin transaction to handle cascading deletes properly
            DB::beginTransaction();
            
            // The related check-ins will be automatically deleted due to 
            // the foreign key constraint with onDelete('cascade')
            $day->delete();
            
            DB::commit();
            
            return redirect()->route('admin.conference-days.index')
                             ->with('success', 'Conference day deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.conference-days.index')
                             ->with('error', 'Failed to delete conference day. ' . $e->getMessage());
        }
    }
} 