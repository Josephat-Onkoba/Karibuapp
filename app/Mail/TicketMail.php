<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;
use App\Models\ConferenceDay;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
     * The conference days.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public $conferenceDays;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->conferenceDays = ConferenceDay::all();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->subject('Your ZURIW25 Conference Details')
                    ->view('emails.ticket', [
                        'ticket' => $this->ticket,
                        'conferenceDays' => $this->conferenceDays
                    ]);

        $programPath = storage_path('app/public/ZURIW25-Program.pdf');
        
        if (file_exists($programPath)) {
            $mail->attach($programPath, [
                'as' => 'ZURIW25-Program.pdf',
                'mime' => 'application/pdf',
            ]);
        } else {
            Log::warning('Conference program PDF not found', [
                'path' => $programPath,
                'ticket_number' => $this->ticket->ticket_number
            ]);
        }

        return $mail;
    }
}
