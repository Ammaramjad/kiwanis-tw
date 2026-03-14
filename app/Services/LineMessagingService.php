<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineMessagingService
{
    protected string $channelAccessToken;
    protected string $apiBase = 'https://api.line.me/v2/bot';

    public function __construct()
    {
        $this->channelAccessToken = config('services.line.channel_access_token', '');
    }

    public function broadcast(string $message): bool
    {
        if (empty($this->channelAccessToken)) {
            Log::warning('LINE channel access token not configured');
            return false;
        }
        try {
            $response = Http::withToken($this->channelAccessToken)
                ->post("{$this->apiBase}/message/broadcast", [
                    'messages' => [['type' => 'text', 'text' => $message]],
                ]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('LINE broadcast failed: ' . $e->getMessage());
            return false;
        }
    }

    public function pushMessage(string $userId, string $message): bool
    {
        if (empty($this->channelAccessToken)) {
            Log::warning('LINE channel access token not configured');
            return false;
        }
        try {
            $response = Http::withToken($this->channelAccessToken)
                ->post("{$this->apiBase}/message/push", [
                    'to' => $userId,
                    'messages' => [['type' => 'text', 'text' => $message]],
                ]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('LINE push message failed: ' . $e->getMessage());
            return false;
        }
    }

    public function multicast(array $userIds, string $message): bool
    {
        if (empty($this->channelAccessToken)) {
            Log::warning('LINE channel access token not configured');
            return false;
        }
        try {
            $response = Http::withToken($this->channelAccessToken)
                ->post("{$this->apiBase}/message/multicast", [
                    'to' => $userIds,
                    'messages' => [['type' => 'text', 'text' => $message]],
                ]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('LINE multicast failed: ' . $e->getMessage());
            return false;
        }
    }
}
