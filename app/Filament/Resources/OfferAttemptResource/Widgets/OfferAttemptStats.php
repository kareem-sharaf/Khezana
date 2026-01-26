<?php

namespace App\Filament\Resources\OfferAttemptResource\Widgets;

use App\Models\OfferAttempt;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OfferAttemptStats extends BaseWidget
{
    protected function getStats(): array
    {
        $today = OfferAttempt::whereDate('created_at', today())->count();
        $thisWeek = OfferAttempt::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonth = OfferAttempt::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $total = OfferAttempt::count();

        $whatsappCount = OfferAttempt::where('channel', 'whatsapp')->count();
        $telegramCount = OfferAttempt::where('channel', 'telegram')->count();

        $sellCount = OfferAttempt::where('operation_type', 'sell')->count();
        $rentCount = OfferAttempt::where('operation_type', 'rent')->count();
        $donateCount = OfferAttempt::where('operation_type', 'donate')->count();

        return [
            Stat::make('عروض اليوم', $today)
                ->description('عدد العروض اليوم')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),
            Stat::make('هذا الأسبوع', $thisWeek)
                ->description('عدد العروض هذا الأسبوع')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('هذا الشهر', $thisMonth)
                ->description('عدد العروض هذا الشهر')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
            Stat::make('واتساب', $whatsappCount)
                ->description('إجمالي عروض واتساب')
                ->descriptionIcon('heroicon-m-chat-bubble-left')
                ->color('success'),
            Stat::make('تلغرام', $telegramCount)
                ->description('إجمالي عروض تلغرام')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('info'),
            Stat::make('بيع', $sellCount)
                ->description('عروض البيع')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            Stat::make('تأجير', $rentCount)
                ->description('عروض التأجير')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('تبرع', $donateCount)
                ->description('عروض التبرع')
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger'),
            Stat::make('الإجمالي', $total)
                ->description('إجمالي جميع العروض')
                ->descriptionIcon('heroicon-m-inbox-stack')
                ->color('primary'),
        ];
    }
}
