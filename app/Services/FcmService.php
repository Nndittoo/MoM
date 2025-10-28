<?php

namespace App\Services;

use GuzzleHttp\Client;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected $client;
    protected $googleClient;

    public function __construct()
    {
        $this->client = new Client([
            'timeout'  => 5.0,
        ]);

        $this->googleClient = new GoogleClient();
        $this->googleClient->setAuthConfig(storage_path('app/firebase/firebase-service-account.json'));
        $this->googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }

    public function sendNotification(array $tokens, string $title, string $body, array $data = [])
    {
        if (empty($tokens)) {
            Log::warning('FCM: No tokens provided');
            return false;
        }

        $projectId = env('FIREBASE_PROJECT_ID');

        try {
            $accessToken = $this->googleClient->fetchAccessTokenWithAssertion()['access_token'];
        } catch (\Exception $e) {
            Log::error('FCM: Failed to get access token - ' . $e->getMessage());
            return false;
        }

        $successCount = 0;
        $failedTokens = [];

        foreach ($tokens as $token) {
            try {
                $payload = [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                        'data' => array_map('strval', $data),
                        'webpush' => [
                            'notification' => [
                                'icon' => '/logo.png',
                                'badge' => '/logo.png',
                                'requireInteraction' => true,
                            ],
                            'fcm_options' => [
                                'link' => url('/'),
                            ]
                        ]
                    ]
                ];

                $response = $this->client->post(
                    "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                    [
                        'headers' => [
                            'Authorization' => "Bearer $accessToken",
                            'Content-Type'  => 'application/json'
                        ],
                        'json' => $payload
                    ]
                );

                $successCount++;

            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                Log::warning("FCM: Failed to send to token {$token}: {$responseBody}");
                $failedTokens[] = $token;

                // Hapus token yang invalid
                if (strpos($responseBody, 'UNREGISTERED') !== false ||
                    strpos($responseBody, 'INVALID_ARGUMENT') !== false) {
                    \App\Models\DeviceToken::where('token', $token)->delete();
                }
            } catch (\Exception $e) {
                Log::error("FCM: Unexpected error for token {$token}: " . $e->getMessage());
                $failedTokens[] = $token;
            }
        }

        Log::info("FCM: Sent {$successCount}/" . count($tokens) . " notifications successfully");

        return $successCount > 0;
    }
}
