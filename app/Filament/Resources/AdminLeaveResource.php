<?php

namespace App\Filament\Resources;

use Filament\Tables;
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
use App\Filament\Resources\AdminLeaveResource\Pages;
use Filament\Schemas\Components\Utilities\Get;

class AdminLeaveResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';
    protected static string|\UnitEnum|null $navigationGroup = 'Leave Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'Admin Leave';
    protected static ?string $pluralLabel = 'Admin Leaves';
    protected static ?string $slug = 'admin-leaves';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('Select Admin')
                ->options(fn () => User::where('role', 'admin')->where('status', 'active')->pluck('name', 'id'))
                ->searchable()
                ->required(),
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


        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query->whereHas('employee', fn ($q) => $q->where('role', 'admin'));
            })
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')->label('Admin')->searchable()->sortable(),
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
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListAdminLeaves::route('/'),
            'create' => Pages\CreateAdminLeave::route('/create'),
            'view'   => Pages\ViewAdminLeave::route('/{record}'),
            'edit'   => Pages\EditAdminLeave::route('/{record}/edit'),
        ];
    }
}
