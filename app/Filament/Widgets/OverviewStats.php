<?php

namespace App\Filament\Widgets;

use App\Models\Pessoa;
use App\Models\Programa;
use App\Models\Subprograma;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OverviewStats extends BaseWidget
{
    protected static bool $isLazy = false;
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Usuários', User::count())
                ->description('O total de usuários')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total de Beneficiários', Pessoa::count())
                ->description('O total de beneficiários')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([17, 2, 10, 3, 15, 4, 2])
                ->color('info'),
            Stat::make('Total de Programas', Programa::count())
                ->description('O total de programas')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('warning'),
            Stat::make('Total de Subprogramas', Subprograma::count())
                ->description('O total de subprogramas')
                ->descriptionIcon('heroicon-m-document')
                ->chart([17, 2, 10, 3, 15, 4, 2])
                ->color('success'),
        ];
    }
}
