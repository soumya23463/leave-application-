<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\LeaveCalculator;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\LeaveRequestResource\Pages;
use Filament\Schemas\Components\Utilities\Get;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|\UnitEnum|null $navigationGroup = 'Leave Management';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('Employee')
                ->options(fn () => User::where('role', 'employee')->where('status', 'active')->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->hidden(fn () => auth()->user()?->hasRole('employee')),
            DatePicker::make('from_date')
                ->required()
                ->live()
                ->native(false)
                ->displayFormat('d F Y'),
            DatePicker::make('to_date')
                ->required()
                ->live()
                ->afterOrEqual('from_date')
                ->native(false)
                ->displayFormat('d F Y'),
            TextEntry::make('days_preview')
                ->label('Working Days')
                ->state(function (Get $get) {
                    $from = $get('from_date');
                    $to = $get('to_date');
                    if ($from && $to) {
                        return LeaveCalculator::calculate($from, $to) . ' day(s)';
                    }
                    return '—';
                }),
            Textarea::make('reason')->required()->rows(3),
            Select::make('status')
                ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                ->default('pending')
                ->hidden(fn () => auth()->user()?->hasRole('employee')),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query->whereHas('employee', fn ($q) => $q->where('role', 'employee'));
                if (auth()->user()?->hasRole('employee')) {
                    $query->where('user_id', auth()->id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('from_date')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('to_date')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('days_requested')->label('Days'),
                Tables\Columns\TextColumn::make('approvedBy.name')->label('Approved By')->default('—'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y, h:i A')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
            ])
            ->actions([
                \Filament\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (LeaveRequest $record) => $record->status === 'pending' && auth()->user()?->hasRole('admin'))
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record) {
                        $days = LeaveCalculator::calculate($record->from_date, $record->to_date);
                        $record->update([
                            'status' => 'approved',
                            'days_requested' => $days,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);

                        // Sirf employee ka balance update karo
                        if ($record->employee?->isEmployee()) {
                            $balance = LeaveBalance::where([
                                'user_id' => $record->user_id,
                                'year'        => $record->from_date->year,
                            ])->first();

                            if ($balance) {
                                $balance->update([
                                    'used_days'      => $balance->used_days + $days,
                                    'remaining_days' => $balance->remaining_days - $days,
                                ]);
                            }
                        }

                        if ($record->employee) {
                            Notification::make()
                                ->title('Leave Approved')
                                ->body('Your leave request has been approved.')
                                ->success()
                                ->sendToDatabase($record->employee);
                        }

                        Notification::make()->title('Leave request approved.')->success()->send();
                    }),

                \Filament\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (LeaveRequest $record) => $record->status === 'pending' && auth()->user()?->hasRole('admin'))
                    ->form([
                        Textarea::make('rejection_reason')->label('Reason for Rejection')->required(),
                    ])
                    ->action(function (LeaveRequest $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);

                        if ($record->employee) {
                            Notification::make()
                                ->title('Leave Rejected')
                                ->body('Your leave request has been rejected.')
                                ->danger()
                                ->sendToDatabase($record->employee);
                        }

                        Notification::make()->title('Leave request rejected.')->danger()->send();
                    }),

                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make()
                    ->visible(fn (LeaveRequest $record) => $record->status === 'pending'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'view' => Pages\ViewLeaveRequest::route('/{record}'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
