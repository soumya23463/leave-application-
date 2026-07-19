<?php

namespace App\Observers;

use App\Models\Holiday;
use App\Models\User;
use App\Services\DiscordService;

class HolidayObserver
{
    /**
     * Handle the Holiday "created" event.
     */
    public function created(Holiday $holiday): void
    {
        $this->dmAllConnectedUsers(
            '🎉 New Holiday Added',
            [
                'Holiday'     => $holiday->name,
                'Date'        => $holiday->date?->format('d M Y') ?? 'N/A',
                'Description' => $holiday->description ?? 'National Holiday',
            ],
            0x800080, // Purple
            "A new company holiday has been added to the calendar.",
            'holiday_created_dm',
            config('app.url') . '/holidays/' . $holiday->id
        );
    }

    /**
     * Handle the Holiday "updated" event.
     */
    public function updated(Holiday $holiday): void
    {
        // Only trigger if important attributes were changed (ignoring timestamps)
        $importantAttributes = ['name', 'date', 'description', 'status'];
        $changed = false;

        foreach ($importantAttributes as $attribute) {
            if ($holiday->wasChanged($attribute)) {
                $changed = true;
                break;
            }
        }

        if ($changed) {
            $updatedBy = auth()->user()?->name ?? 'Admin';

            $this->dmAllConnectedUsers(
                '📝 Holiday Updated',
                [
                    'Holiday'     => $holiday->name,
                    'Date'        => $holiday->date?->format('d M Y') ?? 'N/A',
                    'Updated By'  => $updatedBy,
                    'Description' => $holiday->description ?? 'N/A',
                ],
                0x800080, // Purple
                "Holiday details have been updated.",
                'holiday_updated_dm',
                config('app.url') . '/holidays/' . $holiday->id
            );
        }
    }

    /**
     * Holiday sabhi connected users (jinke paas discord_user_id hai) ko DM karo.
     */
    protected function dmAllConnectedUsers(string $title, array $fields, int $color, string $description, string $eventType, string $link): void
    {
        $discordService = app(DiscordService::class);

        $users = User::whereNotNull('discord_user_id')->get();

        foreach ($users as $user) {
            $discordService->sendDM(
                $user->discord_user_id,
                $title,
                $fields,
                $color,
                $description,
                $eventType,
                $link
            );
        }
    }
}
