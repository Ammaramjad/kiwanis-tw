<?php
namespace App\Filament\Widgets;

use App\Models\Club;
use App\Models\Event;
use App\Models\Member;
use App\Models\Announcement;
use App\Models\JoinApplication;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('總會員數', Member::where('is_active', true)->count())
                ->description('活躍會員')
                ->icon('heroicon-o-users')
                ->color('success'),
            Stat::make('活躍社團', Club::where('is_active', true)->count())
                ->description('個社團')
                ->icon('heroicon-o-building-office')
                ->color('info'),
            Stat::make('即將到來的活動', Event::where('status', 'published')->where('start_time', '>=', now())->count())
                ->description('已發布活動')
                ->icon('heroicon-o-calendar')
                ->color('warning'),
            Stat::make('待審入會申請', JoinApplication::where('status', 'pending')->count())
                ->description('待處理')
                ->icon('heroicon-o-document-text')
                ->color('danger'),
        ];
    }
}
