<?php

namespace App\Filament\Resources;

use App\DTOs\UserDTO;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Services\AdminActionLogService;
use App\Services\UserService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                            ->minLength(8)
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->visible(fn ($livewire) => $livewire instanceof Pages\CreateUser),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                            ->same('password')
                            ->visible(fn ($livewire) => $livewire instanceof Pages\CreateUser),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Roles & Permissions')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->options(Role::pluck('name', 'name'))
                            ->searchable(),
                    ])
                    ->visible(fn () => auth()->user()?->can('manageRoles', User::class)),

                Forms\Components\Section::make('Profile Information')
                    ->schema([
                        Forms\Components\TextInput::make('profile.city')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('profile.address')
                            ->rows(3),
                        Forms\Components\TextInput::make('profile.avatar')
                            ->maxLength(255),
                    ])
                    ->relationship('profile')
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'suspended',
                        'warning' => 'inactive',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
                Tables\Filters\Filter::make('roles')
                    ->form([
                        Forms\Components\Select::make('role')
                            ->options(Role::pluck('name', 'name'))
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['role'],
                                fn (Builder $query, $role): Builder => $query->whereHas('roles', fn ($q) => $q->where('name', $role)),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (User $record) {
                        if ($record->isSuperAdmin()) {
                            throw new \Exception('Cannot delete super admin user');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->isSuperAdmin()) {
                                    throw new \Exception('Cannot delete super admin user');
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['roles', 'profile']);
    }
}
