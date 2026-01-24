<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-folder';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation_groups.content_management');
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getNavigationLabel(): string
    {
        return __('categories.title');
    }

    public static function getModelLabel(): string
    {
        return __('categories.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('categories.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('categories.fields.name'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('categories.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('categories.placeholders.name'))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if (empty($get('slug'))) {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label(__('categories.fields.slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder(__('categories.placeholders.slug'))
                            ->helperText(__('categories.placeholders.slug')),
                        Select::make('parent_id')
                            ->label(__('categories.fields.parent_id'))
                            ->options(function ($record) {
                                $query = Category::query()->orderBy('name');
                                
                                // Exclude current category and its descendants when editing
                                if ($record && $record->exists) {
                                    $excludeIds = [$record->id];
                                    $descendants = $record->descendants()->pluck('id')->toArray();
                                    $excludeIds = array_merge($excludeIds, $descendants);
                                    $query->whereNotIn('id', $excludeIds);
                                }
                                
                                return $query->pluck('name', 'id');
                            })
                            ->searchable()
                            ->nullable()
                            ->placeholder(__('categories.placeholders.parent_id'))
                            ->helperText(__('categories.placeholders.parent_id')),
                        Textarea::make('description')
                            ->label(__('categories.fields.description'))
                            ->rows(3)
                            ->maxLength(65535)
                            ->nullable()
                            ->placeholder(__('categories.placeholders.description')),
                        Checkbox::make('is_active')
                            ->label(__('categories.fields.is_active'))
                            ->default(true),
                    ])->columns(2),
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
                Tables\Columns\TextColumn::make('parent.name')
                    ->label(__('filament-dashboard.Parent'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->default('Root'),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('filament-dashboard.Active'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('children_count')
                    ->label(__('filament-dashboard.Children'))
                    ->counts('children')
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
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label(__('filament-dashboard.Parent Category'))
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('filament-dashboard.Active'))
                    ->placeholder(__('filament-dashboard.All'))
                    ->trueLabel(__('filament-dashboard.Active Only'))
                    ->falseLabel(__('filament-dashboard.Inactive Only')),
                Tables\Filters\Filter::make('roots')
                    ->label(__('filament-dashboard.Root Categories Only'))
                    ->query(fn (Builder $query): Builder => $query->whereNull('parent_id')),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->before(function (Category $record) {
                        if ($record->hasChildren()) {
                            throw new \Exception('Cannot delete category with children. Please delete or move children first.');
                        }
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->hasChildren()) {
                                    throw new \Exception("Cannot delete category '{$record->name}' with children.");
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['parent', 'children']);
    }

    // canViewAny() and canCreate() removed - Filament automatically uses CategoryPolicy
}
