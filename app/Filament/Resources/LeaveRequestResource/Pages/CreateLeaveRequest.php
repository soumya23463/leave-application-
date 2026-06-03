<?php

namespace App\Filament\Resources\LeaveRequestResource\Pages;

use App\Filament\Resources\LeaveRequestResource;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\LeaveCalculator;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        $data['days_requested'] = LeaveCalculator::calculate($data['from_date'], $data['to_date']);

        if ($user->isAdmin()) {
            $data['user_id'] = $data['user_id'] ?? $user->id;
            $data['status']      = 'approved';
            $data['approved_by'] = $user->id;
            $data['approved_at'] = now();
        } else {
            $data['user_id'] = $user->id;
            $data['status']      = 'pending';
        }

        // Duplicate check — same employee, overlapping dates
        $exists = LeaveRequest::where('user_id', $data['user_id'])
            ->where('status', '!=', 'rejected')
            ->where(function ($q) use ($data) {
                $q->whereBetween('from_date', [$data['from_date'], $data['to_date']])
                  ->orWhereBetween('to_date', [$data['from_date'], $data['to_date']])
                  ->orWhere(function ($q) use ($data) {
                      $q->where('from_date', '<=', $data['from_date'])
                        ->where('to_date', '>=', $data['to_date']);
                  });
            })->exists();

        if ($exists) {
            Notification::make()
                ->title('Duplicate Leave Request')
                ->body('A leave request already exists for this period.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'from_date' => 'A leave request already exists for this period.',
            ]);
        }

        $request = LeaveRequest::create($data);

        if ($user->isEmployee()) {
            User::where('role', 'admin')->each(function (User $admin) use ($request) {
                Notification::make()
                    ->title('New Leave Request')
                    ->body("New leave request submitted by {$request->employee?->name}.")
                    ->warning()
                    ->sendToDatabase($admin);
            });
        }

        return $request;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
