<?php

namespace App\Filament\Clusters\Programas\Resources\OrcamentoResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class OrcamentoOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected $listeners = ['updateOrcamentoOverview' => '$refresh'];
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
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

       $media_gasta_por_subprograma = DB::table('subprogramas')
           ->selectRaw('
            AVG(valor) as total
        ')->first();

        $subprogramas = DB::table('subprogramas')
            ->selectRaw(' 
            SUM(valor) as total 
        ')
            ->first();

       $media_gasta_por_subprograma_formatado = number_format($media_gasta_por_subprograma->total, 2, ',', '.');

       $valor_restante = $orcamento_sum->total - $subprogramas->total;
       
        return [
            Stat::make('Orçamentos', $orcamentos->total)
                ->color('warning')
                ->icon('heroicon-m-calculator')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-calculator')
                ->description('Total de Orçamentos'),

            Stat::make('Orcamentados', '$'. $orcamentosFormatados)
                ->color('success')
                ->icon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-banknotes')
                ->description('Total de Valor Orcamentados'),

            Stat::make('Média de Orcamento', '$'.$media_gasta_por_subprograma_formatado)
                ->color('danger')
                ->icon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-banknotes')
                ->description('Média de Orcamentos por Subprograma'),

               
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
