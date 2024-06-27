<?php

namespace App\Filament\Resources\SubprogramaPessoaResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SubprogramaPessoaOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $subprogramapessoa = DB::table('subprograma_pessoas')
            ->selectRaw(' 
            COUNT(*) as total 
        ')
            ->first();

        $rolesCount = DB::table('pessoas')
            ->selectRaw(' 
                COUNT(*) as total,
                SUM(CASE WHEN tipo_pessoa = "Individual" THEN 1 ELSE 0 END) AS individual,
                SUM(CASE WHEN tipo_pessoa = "Institucional" THEN 1 ELSE 0 END) AS institucional,
                SUM(CASE WHEN tipo_pessoa = "Empresa" THEN 1 ELSE 0 END) AS empresa
            ')
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
        
        $totalBeneficiarios = DB::table('subprograma_pessoas')->count();

$media_gasta_por_beneficiario = DB::table('subprograma_pessoas')
    ->selectRaw('
        AVG(subprogramas.valor) as total
    ')
    ->join('subprogramas', 'subprograma_pessoas.id_subprograma', '=', 'subprogramas.id')
    ->first();

$media_gasta_por_beneficiario_formatado = number_format($media_gasta_por_beneficiario->total / $totalBeneficiarios, 2, ',', '.');
        return [
            Stat::make('Total', $subprogramapessoa->total)
                ->color('success')
                ->icon('heroicon-m-newspaper')
                ->chart([5, 30, 10, 50, 10, 4, 47])
                ->descriptionIcon('heroicon-m-newspaper')
                ->description('Total de Patrocínios'),

            Stat::make('Total', $rolesCount->total)
                ->color('warning')
                ->icon('heroicon-s-user-group')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-user-group')
                ->description('Total de Beneficiários'),

            Stat::make('Orcamentados', '$' . $orcamentosFormatados)
                ->color('success')
                ->icon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-banknotes')
                ->description('Total de Valor Orcamentados'),

            Stat::make('Média de Orcamento', '$' . $media_gasta_por_beneficiario_formatado)
                ->color('info')
                ->icon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-banknotes')
                ->description('Média de Gasto por Beneficiário'),
        ];
    }
}
