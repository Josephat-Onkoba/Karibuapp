<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TalkSasaSmsService
{
    protected $apiToken;
    protected $senderId;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiToken = config('talksasa.api_token');
        $this->senderId = config('talksasa.sender_id');
        $this->apiUrl = config('talksasa.api_url');
        
        Log::info('TalkSasaSmsService initialized', [
            'has_token' => !empty($this->apiToken),
            'sender_id' => $this->senderId,
            'api_url' => $this->apiUrl
        ]);
    }

    /**
     * Send an SMS message
     *
     * @param string|array $recipients Phone number(s) to send to
     * @param string $message The message content
     * @param string|null $scheduleTime Optional scheduled time for the message
     * @return array
     */
    public function sendSms($recipients, string $message, ?string $scheduleTime = null)
    {
        try {
            // Convert recipients array to comma-separated string if necessary
            if (is_array($recipients)) {
                $recipients = implode(',', $recipients);
            }

            $payload = [
                'recipient' => $recipients,
                'sender_id' => $this->senderId,
                'type' => 'plain',
                'message' => $message,
            ];

            if ($scheduleTime) {
                $payload['schedule_time'] = $scheduleTime;
            }

            Log::info('Attempting to send SMS', [
                'recipients' => $recipients,
                'message_length' => strlen($message),
                'sender_id' => $this->senderId,
                'has_token' => !empty($this->apiToken),
                'api_url' => $this->apiUrl
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/send', $payload);

            $result = $response->json();

            if ($response->successful()) {
                Log::info('SMS sent successfully', [
                    'recipients' => $recipients,
                    'response' => $result,
                    'status_code' => $response->status()
                ]);
                return [
                    'success' => true,
                    'data' => $result
                ];
            }

            Log::error('Failed to send SMS', [
                'recipients' => $recipients,
                'error' => $result,
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => $result['message'] ?? 'Failed to send SMS'
            ];

        } catch (\Exception $e) {
            Log::error('SMS sending error', [
                'recipients' => $recipients,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to send SMS: ' . $e->getMessage()
            ];
        }
    }
} 