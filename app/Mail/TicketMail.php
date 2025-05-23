<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The ticket instance.
     *
     * @var \App\Models\Ticket
     */
    public $ticket;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $conferenceDays = \App\Models\ConferenceDay::all();
        
        // Generate PDF ticket using Laravel-DomPDF
        $pdf = Pdf::loadView('usher.registration.pdf-ticket', [
            'ticket' => $this->ticket, 
            'conferenceDays' => $conferenceDays
        ]);
        $pdf->setPaper('A4');
        $pdfContent = $pdf->output();
        $filename = "Ticket-{$this->ticket->ticket_number}.pdf";

        return $this
            ->subject('Your ZURIW25 Conference Ticket')
            ->view('emails.ticket')
            ->attachData($pdfContent, $filename, [
                'mime' => 'application/pdf',
            ]);
    }
} 