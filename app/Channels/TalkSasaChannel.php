<?php

namespace App\Channels;

use App\Services\TalkSasaSmsService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * @method array toTalkSasa(mixed $notifiable)
 */
class TalkSasaChannel
{
    protected $smsService;

    public function __construct(TalkSasaSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification&\App\Notifications\TicketSmsNotification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        try {
            if (!method_exists($notification, 'toTalkSasa')) {
                Log::error('Notification does not have toTalkSasa method', [
                    'notification_class' => get_class($notification)
                ]);
                return;
            }

            $message = $notification->toTalkSasa($notifiable);
            
            Log::info('Preparing to send SMS via TalkSasa channel', [
                'phone' => $message['phone'] ?? null,
                'message_length' => strlen($message['message'] ?? ''),
                'notifiable_class' => get_class($notifiable),
                'notification_class' => get_class($notification)
            ]);
            
            if (empty($message['phone']) || empty($message['message'])) {
                Log::error('Invalid SMS message format', [
                    'has_phone' => !empty($message['phone']),
                    'has_message' => !empty($message['message']),
                    'phone' => $message['phone'] ?? null
                ]);
                return;
            }

            // Format phone number to ensure it starts with country code
            $phone = $this->formatPhoneNumber($message['phone']);
            
            $result = $this->smsService->sendSms(
                $phone,
                $message['message']
            );
            
            if (!$result['success']) {
                Log::error('TalkSasa SMS sending failed', [
                    'phone' => $phone,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception in TalkSasa channel', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw to be caught by the registration process
        }
    }

    /**
     * Format phone number to ensure it starts with country code
     * 
     * @param string $phone
     * @return string
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove any spaces, dashes, or other non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If number starts with 0, replace with +254
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        }
        // If number starts with 7 or 1, add 254
        elseif (strlen($phone) === 9 && (substr($phone, 0, 1) === '7' || substr($phone, 0, 1) === '1')) {
            $phone = '254' . $phone;
        }
        // If number doesn't start with 254, add it
        elseif (!str_starts_with($phone, '254')) {
            $phone = '254' . $phone;
        }
        
        return $phone;
    }
} 