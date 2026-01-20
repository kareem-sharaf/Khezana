<?php

namespace App\Filament\Resources;

use App\Enums\AttributeType;
use App\Filament\Resources\AttributeResource\Pages;
use App\Models\Attribute;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-tag';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation_groups.content_management');
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationLabel(): string
    {
        return __('attributes.title');
    }

    public static function getModelLabel(): string
    {
        return __('attributes.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('attributes.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('filament-dashboard.Attribute Information'))
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (empty($set('slug'))) {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('filament-dashboard.Auto-generated from name if left empty')),
                        Select::make('type')
                            ->required()
                            ->options(AttributeType::class)
                            ->native(false)
                            ->helperText(__('filament-dashboard.Select: predefined values, Text: free text, Number: numeric value')),
                        Checkbox::make('is_required')
                            ->label(__('filament-dashboard.Required'))
                            ->helperText(__('filament-dashboard.This attribute must be filled when creating items')),
                    ])->columns(2),
                Section::make(__('filament-dashboard.Predefined Values'))
                    ->schema([
                        Repeater::make('values')
                            ->relationship('values')
                            ->schema([
                                TextInput::make('value')
                                    ->required()
                                    ->maxLength(255)
                                    ->label(__('filament-dashboard.Value')),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['value'] ?? null)
                            ->addActionLabel('Add Value')
                            ->reorderable()
                            ->collapsible()
                            ->visible(fn ($get) => $get('type') === AttributeType::SELECT->value)
                            ->helperText(__('filament-dashboard.Add predefined values for select type attributes')),
                    ])
                    ->collapsible()
                    ->visible(fn ($get) => $get('type') === AttributeType::SELECT->value),
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
                    ->label(__('filament-dashboard.Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('filament-dashboard.Type'))
                    ->badge()
                    ->color(fn (AttributeType $state): string => match ($state) {
                        AttributeType::SELECT => 'success',
                        AttributeType::TEXT => 'info',
                        AttributeType::NUMBER => 'warning',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_required')
                    ->label(__('filament-dashboard.Required'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('values_count')
                    ->label(__('filament-dashboard.Values'))
                    ->counts('values')
                    ->sortable()
                    ->visible(fn (?Attribute $record) => $record?->isSelectType() ?? false),
                Tables\Columns\TextColumn::make('categories_count')
                    ->label(__('filament-dashboard.Categories'))
                    ->counts('categories')
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
                Tables\Filters\SelectFilter::make('type')
                    ->options(AttributeType::class)
                    ->native(false),
                Tables\Filters\TernaryFilter::make('is_required')
                    ->label(__('filament-dashboard.Required'))
                    ->placeholder(__('filament-dashboard.All'))
                    ->trueLabel('Required only')
                    ->falseLabel('Optional only'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAttributes::route('/'),
            'create' => Pages\CreateAttribute::route('/create'),
            'view' => Pages\ViewAttribute::route('/{record}'),
            'edit' => Pages\EditAttribute::route('/{record}/edit'),
        ];
    }

    // canViewAny() and canCreate() removed - Filament automatically uses AttributePolicy
}
