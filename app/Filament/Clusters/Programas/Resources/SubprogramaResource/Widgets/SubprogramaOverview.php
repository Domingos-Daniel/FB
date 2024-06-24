<?php

namespace App\Filament\Clusters\Programas\Resources\SubprogramaResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SubprogramaOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected $listeners = ['updateSubprogramaOverview' => '$refresh'];
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $subprogramas = DB::table('subprogramas')
           ->selectRaw('
                count(*) as total
        ')->first();

        $subprogramas_atribuidos = DB::table('subprogramas')
            ->selectRaw('
                count(*) as total
        ')
        ->join('subprograma_pessoas', 'subprogramas.id', '=', 'subprograma_pessoas.id_subprograma')
        ->first();

        $orcamento_sum = DB::table('orcamentos')
            ->selectRaw('
            SUM(valor) as total 
        ')
            ->join('workflow_orcamento', function ($join) {
                $join->on('orcamentos.id', '=', 'workflow_orcamento.orcamento_id')
                    ->where('workflow_orcamento.status', '=', 'aprovado')
                    ->where('workflow_orcamento.prox_passo', '=', 'Finalizado');
            })
            ->first();
        $orcamentosFormatados = number_format($orcamento_sum->total, 2, ',', '.');
       
        $subprogramasvalor = DB::table('subprogramas')
            ->selectRaw(' 
            SUM(valor) as total 
        ')
            ->first();

       $valor_restante = $orcamento_sum->total - $subprogramasvalor->total;


        return [
            Stat::make('Subprogramas', $subprogramas->total)
                ->color('warning')
                ->icon('heroicon-m-document-text')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-document-text')
                ->description('Total de Subprogramas'),

            Stat::make('Subprogramas Atribuidos', $subprogramas_atribuidos->total)  
                ->color('success')
                ->icon('heroicon-m-document-plus')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-document-plus')
                ->description('Total de Subprogramas Atribuidos'),

            Stat::make('Orcamento Restante', '$'.number_format($valor_restante, 2, ',', '.'))
                ->color($this->getColor($valor_restante))
                ->icon('heroicon-m-currency-dollar')
                ->chart([7, 2, 2, 6, 15, 4, 1])
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->description('Orcamento Restante'),
        ];
    } 

    protected function getColor($valor_restante) {
        if ($valor_restante < 500000) {
            return 'danger';
        }
        return 'success';
    }
}
