<?php

namespace App\Observers;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\DiscordService;

class LeaveRequestObserver
{
    public function created(LeaveRequest $leaveRequest): void
    {
        $employeeName = $leaveRequest->employee?->name ?? 'Unknown Employee';

        if ($leaveRequest->status === 'pending') {
            $link = config('app.url') . '/leave-requests/' . $leaveRequest->id;

            $discordService = app(DiscordService::class);

            // Employee ko confirmation DM
            $employee = $leaveRequest->employee;
            if ($employee && !empty($employee->discord_user_id)) {
                $discordService->sendDM(
                    $employee->discord_user_id,
                    '📢 Leave Request Submitted',
                    [
                        'Leave Type' => 'Annual Leave',
                        'From'       => $leaveRequest->from_date?->format('d M Y') ?? 'N/A',
                        'To'         => $leaveRequest->to_date?->format('d M Y') ?? 'N/A',
                        'Reason'     => $leaveRequest->reason ?? 'No reason provided',
                        'Status'     => 'Pending Approval',
                    ],
                    0xFFFF00,
                    "Your leave request has been submitted and is pending approval.",
                    'leave_request_created_employee_dm',
                    $link
                );
            }

            // Sirf employee ke apne department ke connected admins ko approval DM (requester ko chhod ke)
            $admins = User::where('role', 'admin')
                ->where('department_id', $employee?->department_id)
                ->whereNotNull('discord_user_id')
                ->where('id', '!=', $leaveRequest->user_id)
                ->get();

            foreach ($admins as $admin) {
                $discordService->sendDM(
                    $admin->discord_user_id,
                    '📢 New Leave Request (Action Needed)',
                    [
                        'Employee'   => $employeeName,
                        'Leave Type' => 'Annual Leave',
                        'From'       => $leaveRequest->from_date?->format('d M Y') ?? 'N/A',
                        'To'         => $leaveRequest->to_date?->format('d M Y') ?? 'N/A',
                        'Reason'     => $leaveRequest->reason ?? 'No reason provided',
                        'Status'     => 'Pending Approval',
                    ],
                    0xFFFF00,
                    "A new leave request needs your approval.",
                    'leave_request_created_admin_dm',
                    $link
                );
            }
        } elseif ($leaveRequest->status === 'approved') {
            $this->sendApprovedNotification($leaveRequest);
        }
    }

    public function updated(LeaveRequest $leaveRequest): void
    {
        if ($leaveRequest->wasChanged('status')) {
            if ($leaveRequest->status === 'approved') {
                $this->sendApprovedNotification($leaveRequest);
            } elseif ($leaveRequest->status === 'rejected') {
                $this->sendRejectedNotification($leaveRequest);
            }
        }
    }

    protected function sendApprovedNotification(LeaveRequest $leaveRequest): void
    {
        $employeeName  = $leaveRequest->employee?->name ?? 'Unknown Employee';
        $approvedByName = $leaveRequest->approvedBy?->name ?? auth()->user()?->name ?? 'Admin';
        $approvedDate  = $leaveRequest->approved_at?->format('d M Y') ?? now()->format('d M Y');
        $link          = config('app.url') . '/leave-requests/' . $leaveRequest->id;

        // Sirf usi employee ko DM bhejo jisne leave request ki thi
        $employee = $leaveRequest->employee;
        if ($employee && !empty($employee->discord_user_id)) {
            $discordService = app(DiscordService::class);
            $discordService->sendDM(
                $employee->discord_user_id,
                '✅ Your Leave Request is Approved!',
                [
                    'Leave Type'    => 'Annual Leave',
                    'From'          => $leaveRequest->from_date?->format('d M Y') ?? 'N/A',
                    'To'            => $leaveRequest->to_date?->format('d M Y') ?? 'N/A',
                    'Approved By'   => $approvedByName,
                    'Approved Date' => $approvedDate,
                ],
                0x00FF00,
                "Great news! Your leave request has been approved.",
                'leave_approved_dm',
                $link
            );
        }
    }

    protected function sendRejectedNotification(LeaveRequest $leaveRequest): void
    {
        $employeeName  = $leaveRequest->employee?->name ?? 'Unknown Employee';
        $rejectedByName = auth()->user()?->name ?? 'Admin';
        $link          = config('app.url') . '/leave-requests/' . $leaveRequest->id;

        // Sirf usi employee ko DM bhejo
        $employee = $leaveRequest->employee;
        if ($employee && !empty($employee->discord_user_id)) {
            $discordService = app(DiscordService::class);
            $discordService->sendDM(
                $employee->discord_user_id,
                '❌ Your Leave Request was Rejected',
                [
                    'Leave Type'  => 'Annual Leave',
                    'From'        => $leaveRequest->from_date?->format('d M Y') ?? 'N/A',
                    'To'          => $leaveRequest->to_date?->format('d M Y') ?? 'N/A',
                    'Rejected By' => $rejectedByName,
                ],
                0xFF0000,
                "Unfortunately, your leave request has been rejected.",
                'leave_rejected_dm',
                $link
            );
        }
    }
}
