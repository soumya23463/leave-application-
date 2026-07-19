<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordService
{
    /**
     * Send a DM embed to a specific Discord user via Bot Token.
     */
    public function sendDM(
        string $discordUserId,
        string $title,
        array $fields,
        int $color,
        ?string $description = null,
        ?string $eventType = null,
        ?string $url = null
    ): bool {
        $botToken = config('services.discord.bot_token');

        if (empty($botToken)) {
            Log::warning("Discord Bot Token is not configured. DM was skipped.", [
                'event_type' => $eventType,
                'discord_user_id' => $discordUserId,
            ]);
            return false;
        }

        if (empty($discordUserId)) {
            Log::warning("Discord User ID not set. DM was skipped.", ['event_type' => $eventType]);
            return false;
        }

        try {
            // Step 1: Open DM channel
            $dmResponse = Http::withHeaders([
                'Authorization' => 'Bot ' . $botToken,
                'Content-Type'  => 'application/json',
            ])->post('https://discord.com/api/v10/users/@me/channels', [
                'recipient_id' => $discordUserId,
            ]);

            if ($dmResponse->failed()) {
                $this->logError($eventType, null, "DM channel open failed: " . $dmResponse->status() . ": " . $dmResponse->body());
                return false;
            }

            $channelId = $dmResponse->json('id');

            // Step 2: Send embed message to DM channel
            $embed = $this->buildEmbed($title, $fields, $color, $description, $url);

            $msgResponse = Http::withHeaders([
                'Authorization' => 'Bot ' . $botToken,
                'Content-Type'  => 'application/json',
            ])->post("https://discord.com/api/v10/channels/{$channelId}/messages", [
                'embeds' => [$embed],
            ]);

            if ($msgResponse->failed()) {
                $this->logError($eventType, null, "DM send failed: " . $msgResponse->status() . ": " . $msgResponse->body());
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $this->logError($eventType, null, $e->getMessage());
            return false;
        }
    }

    protected function buildEmbed(string $title, array $fields, int $color, ?string $description, ?string $url): array
    {
        $formattedFields = [];
        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                $formattedFields[] = $value;
            } else {
                $formattedFields[] = [
                    'name'   => (string) $key,
                    'value'  => (string) $value,
                    'inline' => true,
                ];
            }
        }

        $embed = [
            'title'       => $title,
            'description' => $description,
            'color'       => $color,
            'fields'      => $formattedFields,
            'timestamp'   => now()->toIso8601String(),
        ];

        if (!empty($url)) {
            $embed['url'] = (string) $url;
        }

        return $embed;
    }

    protected function logError(?string $eventType, ?int $userId, string $errorMessage): void
    {
        Log::error("Discord API Failure", [
            'event_type'    => $eventType ?? 'unknown',
            'user_id'       => $userId ?? 'system',
            'error_message' => $errorMessage,
            'timestamp'     => now()->toDateTimeString(),
        ]);
    }
}
