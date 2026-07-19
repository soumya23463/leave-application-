<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class DiscordController extends Controller
{
    public function redirect()
    {
        if (!auth()->check()) {
            return redirect()->route('dashboard');
        }

        return Socialite::driver('discord')
            ->scopes(['identify', 'guilds.join'])
            ->redirect();
    }

    public function callback(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('dashboard');
        }

        try {
            $discordUser = Socialite::driver('discord')->user();
            $discordUserId = $discordUser->getId();
            $accessToken = $discordUser->token;

            auth()->user()->update([
                'discord_user_id' => $discordUserId,
            ]);

            // Bot ke server mein automatically add karo
            $guildId = config('services.discord.guild_id');
            $botToken = config('services.discord.bot_token');

            if ($guildId && $botToken && $accessToken) {
                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bot ' . $botToken,
                    'Content-Type'  => 'application/json',
                ])->put("https://discord.com/api/v10/guilds/{$guildId}/members/{$discordUserId}", [
                    'access_token' => $accessToken,
                ]);
            }

            session()->flash('discord_success', 'Discord account connected successfully!');
        } catch (\Throwable $e) {
            session()->flash('discord_error', 'Failed to connect Discord. Please try again.');
        }

        return redirect()->route('dashboard');
    }

    public function disconnect()
    {
        if (!auth()->check()) {
            return redirect()->route('dashboard');
        }

        auth()->user()->update([
            'discord_user_id' => null,
        ]);

        session()->flash('discord_success', 'Discord account disconnected.');

        return redirect()->route('dashboard');
    }
}
