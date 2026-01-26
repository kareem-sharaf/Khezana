<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-building-storefront';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation_groups.administration');
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function getNavigationLabel(): string
    {
        return __('branches.title');
    }

    public static function getModelLabel(): string
    {
        return __('branches.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('branches.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('branches.sections.basic_info'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('branches.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('branches.placeholders.name')),
                        TextInput::make('code')
                            ->label(__('branches.fields.code'))
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->placeholder(__('branches.placeholders.code'))
                            ->helperText(__('branches.hints.code')),
                        TextInput::make('city')
                            ->label(__('branches.fields.city'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('branches.placeholders.city')),
                        TextInput::make('address')
                            ->label(__('branches.fields.address'))
                            ->maxLength(500)
                            ->placeholder(__('branches.placeholders.address')),
                        Checkbox::make('is_active')
                            ->label(__('branches.fields.is_active'))
                            ->default(true),
                    ])->columns(2),

                Section::make(__('branches.sections.contact_info'))
                    ->schema([
                        TextInput::make('phone')
                            ->label(__('branches.fields.phone'))
                            ->tel()
                            ->maxLength(20)
                            ->placeholder(__('branches.placeholders.phone')),
                        TextInput::make('email')
                            ->label(__('branches.fields.email'))
                            ->email()
                            ->maxLength(255)
                            ->placeholder(__('branches.placeholders.email')),
                    ])->columns(2),

                Section::make(__('branches.sections.location'))
                    ->schema([
                        TextInput::make('latitude')
                            ->label(__('branches.fields.latitude'))
                            ->numeric()
                            ->step(0.00000001)
                            ->placeholder(__('branches.placeholders.latitude')),
                        TextInput::make('longitude')
                            ->label(__('branches.fields.longitude'))
                            ->numeric()
                            ->step(0.00000001)
                            ->placeholder(__('branches.placeholders.longitude')),
                    ])->columns(2)
                    ->collapsed(),

                Section::make(__('branches.sections.working_hours'))
                    ->schema([
                        KeyValue::make('working_hours')
                            ->label(__('branches.fields.working_hours'))
                            ->keyLabel(__('branches.fields.day'))
                            ->valueLabel(__('branches.fields.hours'))
                            ->addActionLabel(__('branches.actions.add_day'))
                            ->reorderable(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('filament-dashboard.ID'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('branches.fields.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('branches.fields.code'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('city')
                    ->label(__('branches.fields.city'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label(__('branches.fields.admins_count'))
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('branches.fields.is_active'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-dashboard.Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city')
                    ->label(__('branches.fields.city'))
                    ->options(fn () => Branch::distinct()->pluck('city', 'city')->toArray())
                    ->searchable(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('branches.fields.is_active'))
                    ->placeholder(__('filament-dashboard.All'))
                    ->trueLabel(__('branches.filters.active_only'))
                    ->falseLabel(__('branches.filters.inactive_only')),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->before(function (Branch $record) {
                        if ($record->users()->count() > 0) {
                            throw new \Exception(__('branches.messages.cannot_delete_with_users'));
                        }
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->users()->count() > 0) {
                                    throw new \Exception(__('branches.messages.cannot_delete_with_users'));
                                }
                            }
                        }),
                ]),
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'view' => Pages\ViewBranch::route('/{record}'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('users');
    }

    /**
     * Only super_admin can access this resource.
     */
    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }
}
