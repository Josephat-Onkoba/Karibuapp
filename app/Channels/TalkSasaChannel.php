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
            if (method_exists($notification, 'toTalkSasa')) {
                $message = $notification->toTalkSasa($notifiable);
                
                Log::info('Preparing to send SMS via TalkSasa channel', [
                    'phone' => $message['phone'] ?? null,
                    'message_length' => strlen($message['message'] ?? ''),
                    'notifiable_class' => get_class($notifiable),
                    'notification_class' => get_class($notification)
                ]);
                
                if (!empty($message['phone']) && !empty($message['message'])) {
                    $result = $this->smsService->sendSms(
                        $message['phone'],
                        $message['message']
                    );
                    
                    if (!$result['success']) {
                        Log::error('TalkSasa SMS sending failed', [
                            'phone' => $message['phone'],
                            'error' => $result['error'] ?? 'Unknown error'
                        ]);
                    }
                } else {
                    Log::error('Invalid SMS message format', [
                        'has_phone' => !empty($message['phone']),
                        'has_message' => !empty($message['message']),
                        'phone' => $message['phone'] ?? null
                    ]);
                }
            } else {
                Log::error('Notification does not have toTalkSasa method', [
                    'notification_class' => get_class($notification)
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
} 