<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DailyService
{
    protected string $apiKey;
    protected string $domain;
    protected string $baseUrl = 'https://api.daily.co/v1';

    public function __construct()
    {
        $this->apiKey = config('services.daily.api_key');
        $this->domain = config('services.daily.domain');
    }

    /**
     * Create a Daily.co room for a meeting
     */
    public function createRoom(string $meetingId, array $options = []): ?array
    {
        try {
            $properties = [
                'enable_screenshare' => $options['enable_screenshare'] ?? true,
                'enable_chat' => $options['enable_chat'] ?? true,
                'enable_knocking' => $options['enable_knocking'] ?? false,
                'start_video_off' => $options['start_video_off'] ?? false,
                'start_audio_off' => $options['start_audio_off'] ?? false,
                'lang' => 'fr',
            ];

            // Only add recording if explicitly requested (free plan limitation)
            if (isset($options['enable_recording']) && $options['enable_recording'] === 'cloud') {
                $properties['enable_recording'] = 'cloud';
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/rooms', [
                'name' => $this->generateRoomName($meetingId),
                'privacy' => $options['privacy'] ?? 'public',
                'properties' => $properties,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Daily.co room creation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Daily.co API error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a Daily.co room
     */
    public function deleteRoom(string $roomName): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->delete($this->baseUrl . '/rooms/' . $roomName);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Daily.co room deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get room details
     */
    public function getRoom(string $roomName): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/rooms/' . $roomName);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Daily.co get room failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate meeting token (for private rooms or specific permissions)
     */
    public function createMeetingToken(string $roomName, array $properties = []): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/meeting-tokens', [
                'properties' => array_merge([
                    'room_name' => $roomName,
                    'is_owner' => $properties['is_owner'] ?? false,
                    'user_name' => $properties['user_name'] ?? null,
                ], $properties),
            ]);

            if ($response->successful()) {
                return $response->json()['token'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Daily.co token creation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate room URL
     */
    public function getRoomUrl(string $roomName): string
    {
        return "https://{$this->domain}.daily.co/{$roomName}";
    }

    /**
     * Generate unique room name from meeting ID
     */
    protected function generateRoomName(string $meetingId): string
    {
        return 'meeting-' . $meetingId;
    }

    /**
     * List all recordings for a room
     */
    public function getRecordings(string $roomName): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/recordings', [
                'room_name' => $roomName,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Daily.co get recordings failed: ' . $e->getMessage());
            return null;
        }
    }
}
