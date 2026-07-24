<?php

namespace App\Services;


use Exception;
use GuzzleHttp\Client;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Log;
use Smartisan\Settings\Facades\Settings;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FirebaseService
{
    public $filePath;

    public function sendNotification($data, $fcmTokens, $topicName): void 
    {
        if (config('app.env') === 'local') {
            Log::info('Push notifications are disabled in local environment.');
            return;
        }

        try {
            $notification = Settings::group('notification')->all();

            $url = 'https://fcm.googleapis.com/v1/projects/' . $notification['notification_fcm_project_id'] . '/messages:send';
            $accessToken = $this->getAccessToken();

            $client  = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ];
            foreach ($fcmTokens as $fcmToken) {

                $payload = [
                    'message' => [
                        'token' => $fcmToken,
                        'notification' => [
                            'title' => $data->title,
                            'body' => $data->description,
                            'image' => $data->image ?? null,
                        ],
                        'data' => [
                            'title' => $data->title,
                            'body' => $data->description,
                            'sound' => 'default',
                            'image' => (string) ($data->image ?? ''),
                            'topicName' => $topicName,
                            'orderId' => (string) ($data->order_id ?? ''),
                            'url' => str_contains($topicName, 'order') ? '/admin/online-orders' : '/',
                        ],
                        'android' => [
                            'priority' => 'high',
                            'notification' => [
                                'sound' => 'default',
                                'default_vibrate_timings' => true,
                                'notification_count' => 1,
                            ],
                        ],
                        'webpush' => [
                            "headers" => [
                                "Urgency" => "high",
                                "TTL" => "86400"
                            ],
                        ],
                    ],
                ];


                $result = $client->post($url, [
                    'headers' => $headers,
                    "body"    => json_encode($payload)
                ]);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

    function getAccessToken()
    {

        $setting = NotificationSetting::where(['key' => 'notification_fcm_json_file'])->firstOrFail();
        $this->filePath = $setting->getFirstMediaPath('notification-file');

        $SCOPES = ['https://www.googleapis.com/auth/cloud-platform'];

        if (!$this->filePath || !file_exists($this->filePath)) {
            throw new Exception('Service account key file not found');
        }

        $credentials = new ServiceAccountCredentials($SCOPES, $this->filePath);
        $token = $credentials->fetchAuthToken();

        if (isset($token['access_token'])) {
            return $token['access_token'];
        } else {
            throw new Exception('Failed to fetch access token');
        }
    }
}
