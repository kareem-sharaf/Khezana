<?php

namespace App\Filament\Resources\ContactAttemptResource\Widgets;

use App\Models\ContactAttempt;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContactAttemptStats extends BaseWidget
{
    protected function getStats(): array
    {
        $today = ContactAttempt::whereDate('created_at', today())->count();
        $thisWeek = ContactAttempt::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonth = ContactAttempt::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $total = ContactAttempt::count();

        $whatsappCount = ContactAttempt::where('channel', 'whatsapp')->count();
        $telegramCount = ContactAttempt::where('channel', 'telegram')->count();

        return [
            Stat::make('محاولات اليوم', $today)
                ->description('عدد المحاولات اليوم')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),
            Stat::make('هذا الأسبوع', $thisWeek)
                ->description('عدد المحاولات هذا الأسبوع')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('هذا الشهر', $thisMonth)
                ->description('عدد المحاولات هذا الشهر')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
            Stat::make('واتساب', $whatsappCount)
                ->description('إجمالي محاولات واتساب')
                ->descriptionIcon('heroicon-m-chat-bubble-left')
                ->color('success'),
            Stat::make('تلغرام', $telegramCount)
                ->description('إجمالي محاولات تلغرام')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('info'),
            Stat::make('الإجمالي', $total)
                ->description('إجمالي جميع المحاولات')
                ->descriptionIcon('heroicon-m-inbox-stack')
                ->color('primary'),
        ];
    }
}
