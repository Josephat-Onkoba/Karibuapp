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
        
        if (empty($this->apiToken)) {
            Log::error('TalkSasa API token is not configured');
        }
        
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
        $logContext = [
            'recipients' => is_array($recipients) ? implode(',', $recipients) : $recipients,
            'message_length' => strlen($message),
            'has_api_token' => !empty($this->apiToken),
            'sender_id' => $this->senderId,
            'api_url' => $this->apiUrl,
            'scheduled' => !is_null($scheduleTime)
        ];

        Log::info('Attempting to send SMS', $logContext);

        try {
            // Validate configuration
            if (empty($this->apiToken)) {
                throw new \Exception('TalkSasa API token is not configured. Please check your .env file for TALKSASA_API_TOKEN.');
            }

            if (empty($this->apiUrl)) {
                throw new \Exception('TalkSasa API URL is not configured. Please check your .env file for TALKSASA_API_URL.');
            }

            // Convert recipients array to comma-separated string if necessary
            if (is_array($recipients)) {
                $recipients = implode(',', array_filter($recipients));
            }

            // Validate recipient phone number format
            if (empty($recipients)) {
                throw new \Exception('Recipient phone number is required');
            }


            // Validate message
            if (empty(trim($message))) {
                throw new \Exception('Message content is required');
            }

            // Ensure message is not too long (SMS has character limits)
            if (strlen($message) > 160) {
                Log::warning('SMS message exceeds recommended length', [
                    'length' => strlen($message),
                    'max_recommended' => 160
                ]);
                // Consider truncating or splitting the message in a real-world scenario
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

            $logContext['payload'] = array_merge([], $payload); // Create a copy without modifying the original
            unset($logContext['payload']['message']); // Don't log the full message for privacy

            // Log the API request details (without sensitive data)
            Log::debug('SMS API request prepared', $logContext);

            // Make the API request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->retry(2, 1000) // Retry twice with 1 second delay
            ->post(rtrim($this->apiUrl, '/') . '/send', $payload);

            $result = $response->json() ?? [];

            if ($response->successful()) {
                Log::info('SMS sent successfully', array_merge($logContext, [
                    'response' => $result,
                    'status_code' => $response->status()
                ]));
                
                return [
                    'success' => true,
                    'data' => $result
                ];
            }

            // Handle API error responses
            $statusCode = $response->status();
            $responseBody = $response->body();
            
            // Parse the error message from the response
            $errorMessage = $result['message'] ?? 'Unknown error from API';
            $errorCode = $result['code'] ?? 'unknown';
            
            // Map common HTTP status codes to user-friendly messages
            $statusMessages = [
                400 => 'Bad request. Please check your request parameters.',
                401 => 'Authentication failed. Invalid API token.',
                403 => 'Authorization failed. Check your sender ID or account permissions.',
                404 => 'The requested resource was not found.',
                422 => 'Validation error. Invalid request data.',
                429 => 'Too many requests. Please try again later.',
                500 => 'Internal server error. Please try again later.',
                502 => 'Bad gateway. The server returned an invalid response.',
                503 => 'Service unavailable. Please try again later.',
                504 => 'Gateway timeout. The server took too long to respond.'
            ];
            
            // Use the mapped message or a default one
            $errorMessage = $statusMessages[$statusCode] ?? $errorMessage;
            
            Log::error('Failed to send SMS - API error', array_merge($logContext, [
                'status_code' => $statusCode,
                'error_code' => $errorCode,
                'error_message' => $errorMessage,
                'response_body' => $responseBody,
                'response_headers' => $response->headers()
            ]));

            return [
                'success' => false,
                'error' => $errorMessage,
                'code' => $errorCode,
                'status_code' => $statusCode
            ];

        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            $errorMessage = 'Failed to send SMS: ' . $e->getMessage();
            
            Log::error('SMS sending error', array_merge($logContext, [
                'exception' => get_class($e),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return [
                'success' => false,
                'error' => $errorMessage,
                'code' => 'exception',
                'exception' => get_class($e)
            ];
        }
    }
} 