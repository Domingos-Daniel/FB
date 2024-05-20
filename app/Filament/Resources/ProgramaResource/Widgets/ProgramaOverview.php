<?php

namespace App\Filament\Resources\ProgramaResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ProgramaOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected $listeners = ['updateProgramaOverview' => '$refresh'];
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $programas = DB::table('programas')
            ->selectRaw(' 
            COUNT(*) as total 
        ')
            ->first();

        $subprogramas = DB::table('subprogramas')
            ->selectRaw(' 
            COUNT(*) as total 
        ')
            ->first();

        $orcamentos = DB::table('orcamentos')
            ->selectRaw(' 
            COUNT(*) as total
        ')
            ->first();

        $orcamento_sum = DB::table('orcamentos')
            ->selectRaw('
         SUM(valor) as total
     ')
            ->join('workflow_orcamento', 'orcamentos.id', '=', 'workflow_orcamento.orcamento_id')
            ->first();
        $orcamentosFormatados = number_format($orcamento_sum->total, 2, ',', '.');
       // $orcamento_sum_float = floatval($orcamento_sum->total->value);
        return [
            Stat::make('Total', $programas->total)
                ->color('warning')
                ->icon('heroicon-m-newspaper')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-newspaper')
                ->description('Total de Programas'),

            Stat::make('Subprogramas', $subprogramas->total)
                ->color('danger')
                ->icon('heroicon-s-document-text')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-document-text')
                ->description('Total de Subprogramas'),

            Stat::make('Orçamentos', $orcamentos->total)
                ->color('warning')
                ->icon('heroicon-m-calculator')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-calculator')
                ->description('Total de Orçamentos'),

            Stat::make('Orçamentos Aprovados', '$ '.$orcamentosFormatados)
                ->color('success') 
                ->icon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-banknotes')
                ->description('Total de Orçamentos Aprovados'),
        ];
    }
}
