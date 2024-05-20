<?php

namespace App\Filament\Resources\PessoaResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PessoaOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected $listeners = ['updatePessoaOverview' => '$refresh'];
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $rolesCount = DB::table('pessoas')
        ->selectRaw(' 
            COUNT(*) as total,
            SUM(CASE WHEN tipo_pessoa = "Individual" THEN 1 ELSE 0 END) AS individual,
            SUM(CASE WHEN tipo_pessoa = "Institucional" THEN 1 ELSE 0 END) AS institucional,
            SUM(CASE WHEN tipo_pessoa = "Empresa" THEN 1 ELSE 0 END) AS empresa
        ')
        ->first();
 
        return [
            Stat::make('Total', $rolesCount->total)
                ->color('warning')
                ->icon('heroicon-s-user-group')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-user-group')
                ->description('Total de Pessoas'), 
                
            Stat::make('Individual', $rolesCount->individual)   
                ->color('success')
                ->icon('heroicon-s-user')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-user')
                ->description('Pessoas Individuais'),
                
            Stat::make('Institucional', $rolesCount->institucional)
                ->color('danger')
                ->icon('heroicon-s-building-office-2')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-building-office-2')
                ->description('Pessoas Institstitucional'),

            Stat::make('Empresa', $rolesCount->empresa)
                ->color('info')
                ->icon('heroicon-s-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->description('Pessoas Empresa'),
        ];
    }
}
