<?php

namespace App\Http\Controllers\Usher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketMail;

class TicketNotificationController extends Controller
{
    /**
     * Generate and download a PDF ticket.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf($id)
    {
        try {
            $ticket = Ticket::with(['participant', 'registeredBy'])->findOrFail($id);
            $conferenceDays = \App\Models\ConferenceDay::all();
            
            // Create PDF using Laravel-DomPDF
            $pdf = Pdf::loadView('usher.registration.pdf-ticket', compact('ticket', 'conferenceDays'));
            $pdf->setPaper('A4');
            
            // Log the action for tracking purposes
            Log::info('Ticket PDF generated', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'participant' => $ticket->participant->full_name
            ]);
            
            return $pdf->download("Ticket-{$ticket->ticket_number}.pdf");
        } catch (\Exception $e) {
            Log::error('Failed to generate ticket PDF', [
                'ticket_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to generate PDF ticket: ' . $e->getMessage());
        }
    }
    
    /**
     * Show printable version of a ticket.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printView($id)
    {
        try {
            $ticket = Ticket::with(['participant', 'registeredBy'])->findOrFail($id);
            $conferenceDays = \App\Models\ConferenceDay::all();
            
            return view('usher.registration.print-ticket', compact('ticket', 'conferenceDays'));
        } catch (\Exception $e) {
            Log::error('Failed to load printable ticket view', [
                'ticket_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to load printable ticket: ' . $e->getMessage());
        }
    }

    /**
     * Send ticket via email.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sendEmail($id)
    {
        try {
            $ticket = Ticket::with(['participant', 'registeredBy'])->findOrFail($id);
            
            // Use Laravel's Mail facade to send an email
            Mail::to($ticket->participant->email)->send(new TicketMail($ticket));
            
            // Log the action for tracking purposes
            Log::info('Ticket email sent', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'participant' => $ticket->participant->full_name,
                'email' => $ticket->participant->email
            ]);
            
            return redirect()->back()->with('success', 'Ticket has been sent to ' . $ticket->participant->email);
        } catch (\Exception $e) {
            Log::error('Failed to send ticket email', [
                'ticket_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to send ticket: ' . $e->getMessage());
        }
    }
    
    /**
     * Send ticket via SMS.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sendSms($id)
    {
        try {
            $ticket = Ticket::with(['participant'])->findOrFail($id);
            
            // In a real implementation, this would use an SMS gateway service
            // SmsGateway::send($ticket->participant->phone_number, "Your ZURIW25 Conference ticket: {$ticket->ticket_number}");
            
            // Log the action for demonstration purposes
            Log::info('Ticket SMS sent', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'participant' => $ticket->participant->full_name,
                'phone' => $ticket->participant->phone_number
            ]);
            
            return redirect()->back()->with('success', 'Ticket has been sent to ' . $ticket->participant->phone_number);
        } catch (\Exception $e) {
            Log::error('Failed to send ticket SMS', [
                'ticket_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to send ticket SMS: ' . $e->getMessage());
        }
    }
    
    /**
     * Send ticket notifications after check-in.
     *
     * @param  int  $participantId
     * @return \Illuminate\Http\Response
     */
    public function sendAfterCheckIn($participantId)
    {
        try {
            // Get the most recent active ticket for this participant
            $ticket = Ticket::where('participant_id', $participantId)
                ->where('active', true)
                ->with(['participant'])
                ->latest()
                ->firstOrFail();
            
            // Send email
            Mail::to($ticket->participant->email)->send(new TicketMail($ticket));
            
            // Log email action
            Log::info('Ticket email sent after check-in', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'participant' => $ticket->participant->full_name,
                'email' => $ticket->participant->email
            ]);
            
            // Log SMS action (SMS is mocked)
            Log::info('Ticket SMS sent after check-in', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'participant' => $ticket->participant->full_name,
                'phone' => $ticket->participant->phone_number
            ]);
            
            return redirect()->back()->with('success', 'Ticket notifications sent successfully');
        } catch (\Exception $e) {
            Log::error('Failed to send ticket notifications after check-in', [
                'participant_id' => $participantId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to send ticket notifications: ' . $e->getMessage());
        }
    }
}
