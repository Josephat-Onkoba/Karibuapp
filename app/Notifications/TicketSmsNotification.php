<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Services\TalkSasaSmsService;
use Illuminate\Notifications\Notification;

class TicketSmsNotification extends Notification
{
    protected $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['talksasa'];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toTalkSasa($notifiable)
    {
        $validDays = [];
        if ($this->ticket->day1_valid) $validDays[] = 'Day 1';
        if ($this->ticket->day2_valid) $validDays[] = 'Day 2';
        if ($this->ticket->day3_valid) $validDays[] = 'Day 3';

        $message = "Dear {$this->ticket->participant->full_name},\n\n";
        $message .= "Your RIW25 Conference ticket has been generated.\n";
        $message .= "Access Ticket: {$this->ticket->ticket_number}\n";
        $message .= "Valid for: " . implode(', ', $validDays) . "\n";
        $message .= "Thank you for participating in the 7th Zetech Research and Innovation Week 2025 Conference!";

        return [
            'phone' => $notifiable->phone_number,
            'message' => $message
        ];
    }
} 