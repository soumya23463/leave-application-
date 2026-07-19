<?php

namespace App\Observers;

use App\Models\User;
use App\Services\DiscordService;

class UserObserver
{
    public function created(User $user): void
    {
        if ($user->role !== 'employee') {
            return;
        }

        $discordService = app(DiscordService::class);

        // Naye employee ko welcome DM (agar usne Discord connect kiya hai)
        if (!empty($user->discord_user_id)) {
            $discordService->sendDM(
                $user->discord_user_id,
                '👋 Welcome to Leave Management System',
                [
                    'Name'         => $user->name,
                    'Email'        => $user->email,
                    'Joining Date' => $user->joining_date?->format('d M Y') ?? 'N/A',
                ],
                0x0000FF,
                "Your account has been created. You can now log in and manage your leave requests.",
                'employee_welcome_dm',
                config('app.url')
            );
        }

        // Sabhi connected admins ko naye employee ki DM
        foreach ($this->connectedAdmins($user->id) as $admin) {
            $discordService->sendDM(
                $admin->discord_user_id,
                '👤 New Employee Added',
                [
                    'Employee Name' => $user->name,
                    'Joining Date'  => $user->joining_date?->format('d M Y') ?? 'N/A',
                    'Role'          => ucfirst($user->role),
                ],
                0x0000FF,
                "A new employee account has been created in the system.",
                'employee_created_admin_dm',
                config('app.url') . '/employees/' . $user->id
            );
        }
    }

    public function updated(User $user): void
    {
        if ($user->role === 'employee' && $user->wasChanged('status')) {
            $updatedBy = auth()->user()?->name ?? 'Admin';
            $discordService = app(DiscordService::class);

            // Employee ko uske status change ki DM
            if (!empty($user->discord_user_id)) {
                $discordService->sendDM(
                    $user->discord_user_id,
                    '📢 Your Account Status Changed',
                    [
                        'Status'     => ucfirst($user->status),
                        'Updated By' => $updatedBy,
                    ],
                    $user->status === 'active' ? 0x00FF00 : 0xFF0000,
                    "Your account status has been updated by the administrator.",
                    'employee_status_dm'
                );
            }

            // Sabhi connected admins ko status change ki DM
            foreach ($this->connectedAdmins($user->id) as $admin) {
                $discordService->sendDM(
                    $admin->discord_user_id,
                    '⚠ Employee Status Changed',
                    [
                        'Employee'   => $user->name,
                        'Status'     => ucfirst($user->status),
                        'Updated By' => $updatedBy,
                    ],
                    0x0000FF,
                    "An employee's status has been modified.",
                    'employee_status_admin_dm',
                    config('app.url') . '/employees/' . $user->id
                );
            }
        }
    }

    /**
     * Connected admins (jinke paas discord_user_id hai), diye gaye user ko chhod ke.
     */
    protected function connectedAdmins(?int $excludeUserId = null)
    {
        return User::where('role', 'admin')
            ->whereNotNull('discord_user_id')
            ->when($excludeUserId, fn ($q) => $q->where('id', '!=', $excludeUserId))
            ->get();
    }
}
