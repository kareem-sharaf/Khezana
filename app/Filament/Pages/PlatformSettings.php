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
        return __('filament-dashboard.Platform Settings');
    }

    public function getTitle(): string
    {
        return __('filament-dashboard.Platform Settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-dashboard.Settings');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'delivery_service_fee_percent' => (string) Setting::deliveryServiceFeePercent(),
            'price_slider_min' => (string) Setting::priceSliderMin(),
            'price_slider_max' => (string) Setting::priceSliderMax(),
            'price_slider_step' => (string) Setting::priceSliderStep(),
            'price_slider_min_gap' => (string) Setting::priceSliderMinGap(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make(__('filament-dashboard.Listing & Fees'))
                    ->description(__('filament-dashboard.Configure delivery/service fee shown to sellers and applied to displayed prices.'))
                    ->schema([
                        TextInput::make('delivery_service_fee_percent')
                            ->label(__('filament-dashboard.Delivery / service fee (%)'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.5)
                            ->suffix('%')
                            ->default(10)
                            ->required()
                            ->helperText(__('filament-dashboard.A fee added to the product price. Displayed price = base price + this %. Sellers see this in the pre-creation notice.')),
                    ])
                    ->columns(1),
                Section::make(__('filament-dashboard.Price Filter Settings'))
                    ->description(__('filament-dashboard.Configure price slider range and step values for the item filters.'))
                    ->schema([
                        TextInput::make('price_slider_min')
                            ->label(__('filament-dashboard.Price slider minimum value'))
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->helperText(__('filament-dashboard.Minimum price value shown in the price filter slider.')),
                        TextInput::make('price_slider_max')
                            ->label(__('filament-dashboard.Price slider maximum value'))
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->helperText(__('filament-dashboard.Maximum price value shown in the price filter slider.')),
                        TextInput::make('price_slider_step')
                            ->label(__('filament-dashboard.Price slider step'))
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->helperText(__('filament-dashboard.Increment step value for the price slider (e.g., 100, 500, 1000).')),
                        TextInput::make('price_slider_min_gap')
                            ->label(__('filament-dashboard.Price slider minimum gap'))
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->helperText(__('filament-dashboard.Minimum gap between minimum and maximum price values in the slider.')),
                    ])
                    ->columns(2),
            ]);
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-dashboard.Save'))
                ->action(function () {
                    $this->form->validate();
                    $this->save();
                })
                ->keyBindings(['mod+s']),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $percent = (float) ($data['delivery_service_fee_percent'] ?? 10);
        $percent = max(0, min(100, $percent));

        Setting::set('delivery_service_fee_percent', (string) $percent);

        // Price slider settings - all values must come from form data
        $priceMin = max(0, (float) ($data['price_slider_min'] ?? Setting::priceSliderMin()));
        $priceMax = max(1, (float) ($data['price_slider_max'] ?? Setting::priceSliderMax()));
        $priceStep = max(1, (float) ($data['price_slider_step'] ?? Setting::priceSliderStep()));
        $priceMinGap = max(0, (float) ($data['price_slider_min_gap'] ?? Setting::priceSliderMinGap()));

        // Ensure min < max
        if ($priceMin >= $priceMax) {
            Notification::make()
                ->title(__('filament-dashboard.Validation error'))
                ->body(__('filament-dashboard.Minimum price must be less than maximum price.'))
                ->danger()
                ->send();
            return;
        }

        Setting::set('price_slider_min', (string) $priceMin);
        Setting::set('price_slider_max', (string) $priceMax);
        Setting::set('price_slider_step', (string) $priceStep);
        Setting::set('price_slider_min_gap', (string) $priceMinGap);

        Notification::make()
            ->title(__('filament-dashboard.Settings saved'))
            ->success()
            ->send();

        $this->form->fill([
            'delivery_service_fee_percent' => (string) $percent,
            'price_slider_min' => (string) $priceMin,
            'price_slider_max' => (string) $priceMax,
            'price_slider_step' => (string) $priceStep,
            'price_slider_min_gap' => (string) $priceMinGap,
        ]);
    }
}
