<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PlatformSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $slug = 'platform-settings';

    protected string $view = 'filament.pages.platform-settings';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationLabel(): string
    {
        return 'Platform Settings';
    }

    public function getTitle(): string
    {
        return 'Platform Settings';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'delivery_service_fee_percent' => (string) Setting::deliveryServiceFeePercent(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make('Listing & Fees')
                    ->description('Configure delivery/service fee shown to sellers and applied to displayed prices.')
                    ->schema([
                        TextInput::make('delivery_service_fee_percent')
                            ->label('Delivery / service fee (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.5)
                            ->suffix('%')
                            ->default(10)
                            ->required()
                            ->helperText('A fee added to the product price. Displayed price = base price + this %. Sellers see this in the pre-creation notice.'),
                    ])
                    ->columns(1),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $percent = (float) ($data['delivery_service_fee_percent'] ?? 10);
        $percent = max(0, min(100, $percent));

        Setting::set('delivery_service_fee_percent', (string) $percent);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();

        $this->form->fill([
            'delivery_service_fee_percent' => (string) $percent,
        ]);
    }
}
