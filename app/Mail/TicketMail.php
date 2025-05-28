<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;

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
        return $this
            ->subject('Your ZURIW25 Conference Details')
            ->view('emails.ticket')
            ->attach(storage_path('app/public/ZURIW25-Program.pdf'), [
                'as' => 'ZURIW25-Program.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
