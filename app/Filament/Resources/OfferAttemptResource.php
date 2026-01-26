<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\OfferAttemptResource\Pages;
use App\Models\OfferAttempt;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OfferAttemptResource extends Resource
{
    protected static ?string $model = OfferAttempt::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-gift';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'التقارير';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getModelLabel(): string
    {
        return 'محاولة عرض';
    }

    public static function getPluralModelLabel(): string
    {
        return 'محاولات العروض';
    }

    public static function getNavigationLabel(): string
    {
        return 'محاولات العروض (الطلبات)';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereDate('created_at', today())->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('المستخدم')
                    ->disabled(),
                Forms\Components\Select::make('request_id')
                    ->relationship('request', 'title')
                    ->label('الطلب')
                    ->disabled(),
                Forms\Components\TextInput::make('channel')
                    ->label('قناة التواصل')
                    ->disabled(),
                Forms\Components\TextInput::make('operation_type')
                    ->label('نوع العملية')
                    ->disabled(),
                Forms\Components\TextInput::make('price')
                    ->label('السعر')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('request.title')
                    ->label('الطلب')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('channel')
                    ->label('القناة')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'whatsapp' => 'success',
                        'telegram' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'whatsapp' => 'واتساب',
                        'telegram' => 'تلغرام',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('operation_type')
                    ->label('نوع العملية')
                    ->badge()
                    ->color(fn (?string $state): string => match($state) {
                        'sell' => 'success',
                        'rent' => 'warning',
                        'donate' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'sell' => 'بيع',
                        'rent' => 'تأجير',
                        'donate' => 'تبرع',
                        default => '-',
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('SYP')
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->label('القناة')
                    ->options([
                        'whatsapp' => 'واتساب',
                        'telegram' => 'تلغرام',
                    ]),
                Tables\Filters\SelectFilter::make('operation_type')
                    ->label('نوع العملية')
                    ->options([
                        'sell' => 'بيع',
                        'rent' => 'تأجير',
                        'donate' => 'تبرع',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions - read only
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListOfferAttempts::route('/'),
            'view' => Pages\ViewOfferAttempt::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
