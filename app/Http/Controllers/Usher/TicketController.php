<?php

namespace App\Http\Controllers\Usher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\ConferenceDay;

class TicketController extends Controller
{
    /**
     * Display the tickets search page.
     */
    public function index()
    {
        return view('usher.tickets.index');
    }
    
    /**
     * Search for a ticket by ticket number.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'ticket_number' => 'required|string|max:4',
        ]);
        
        // Add the standard prefix to the ticket number
        $fullTicketNumber = 'ZU-RIW25-' . $validated['ticket_number'];
        
        $ticket = Ticket::where('ticket_number', $fullTicketNumber)
            ->with(['participant', 'registeredBy'])
            ->first();
            
        if (!$ticket) {
            return redirect()->back()->with('error', 'Ticket not found. Please check the 4-digit ticket number and try again.');
        }
        
        // Get active conference days for displaying valid days
        $conferenceDays = ConferenceDay::where('active', true)
            ->orderBy('date')
            ->get();
            
        // Get today's conference day if available
        $today = ConferenceDay::getToday();
        
        return view('usher.tickets.view', compact('ticket', 'conferenceDays', 'today'));
    }
} 