<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DiscordService;
use Illuminate\Console\Command;

class SendBirthdayWishes extends Command
{
    protected $signature = 'birthday:wish';
    protected $description = 'On each user\'s birthday, DM everyone (except super admins) on Discord';

    public function handle(DiscordService $discord): int
    {
        $today = now();

        // Whose birthday is today (match month + day, ignore year).
        $celebrants = User::whereNotNull('dob')
            ->whereMonth('dob', $today->month)
            ->whereDay('dob', $today->day)
            ->get();

        if ($celebrants->isEmpty()) {
            $this->info('No birthdays today.');
            return Command::SUCCESS;
        }

        foreach ($celebrants as $celebrant) {
            // Wish the birthday person themselves (if connected).
            if (! empty($celebrant->discord_user_id)) {
                $discord->sendDM(
                    $celebrant->discord_user_id,
                    '🎂 Happy Birthday, ' . $celebrant->name . '!',
                    ['Date' => $today->format('d M')],
                    0xFF69B4,
                    'Wishing you a wonderful day from the whole team! 🎉',
                    'birthday_self',
                );
            }

            // Notify everyone else — except super admins and the celebrant.
            $recipients = User::whereNotNull('discord_user_id')
                ->where('role', '!=', 'superadmin')
                ->where('id', '!=', $celebrant->id)
                ->get();

            foreach ($recipients as $recipient) {
                $discord->sendDM(
                    $recipient->discord_user_id,
                    '🎉 It\'s ' . $celebrant->name . '\'s Birthday!',
                    [
                        'Name' => $celebrant->name,
                        'Date' => $today->format('d M'),
                    ],
                    0xFF69B4,
                    "Today is {$celebrant->name}'s birthday — don't forget to wish them! 🎂",
                    'birthday_notice',
                );
            }

            $this->info("Sent birthday notifications for {$celebrant->name} to {$recipients->count()} people.");
        }

        return Command::SUCCESS;
    }
}
