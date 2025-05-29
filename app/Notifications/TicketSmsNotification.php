<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Services\TalkSasaSmsService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

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
        // Ensure the notifiable has a phone number
        if (empty($notifiable->phone_number)) {
            Log::warning('Cannot send SMS: No phone number provided for participant', [
                'participant_id' => $notifiable->id,
                'ticket_id' => $this->ticket->id
            ]);
            return [];
        }
        
        return ['talksasa'];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toTalkSasa($notifiable)
    {
        try {
            // Ensure the ticket has a participant relationship loaded
            if (!$this->ticket->relationLoaded('participant')) {
                $this->ticket->load('participant');
            }

            if (!$this->ticket->participant) {
                throw new \RuntimeException('No participant associated with this ticket');
            }

            $validDays = [];
            if ($this->ticket->day1_valid) $validDays[] = 'Day 1';
            if ($this->ticket->day2_valid) $validDays[] = 'Day 2';
            if ($this->ticket->day3_valid) $validDays[] = 'Day 3';

            if (empty($validDays)) {
                $validDays[] = 'Specific Day'; // Fallback if no days are marked as valid
            }

            $message = "Dear {$this->ticket->participant->full_name},\n\n";
            $message .= "Your RIW25 Conference ticket has been generated.\n";
            $message .= "Access Ticket: {$this->ticket->ticket_number}\n";
            $message .= "Valid for: " . implode(', ', $validDays) . "\n";
            $message .= "For more information about the conference, visit https://conference.zetech.ac.ke. You can also view the full program at https://conference.zetech.ac.ke/index.php/conference-2025/program";

            Log::info('Preparing to send SMS notification', [
                'participant_id' => $notifiable->id,
                'phone' => $notifiable->phone_number,
                'ticket_number' => $this->ticket->ticket_number
            ]);

            return [
                'phone' => $notifiable->phone_number,
                'message' => $message
            ];

        } catch (\Exception $e) {
            Log::error('Failed to prepare SMS notification', [
                'error' => $e->getMessage(),
                'ticket_id' => $this->ticket->id,
                'participant_id' => $notifiable->id ?? 'unknown'
            ]);
            
            throw $e; // Re-throw to allow the notification system to handle the failure
        }
    }
} 