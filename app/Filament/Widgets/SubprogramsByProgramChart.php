<?php

namespace App\Filament\Widgets;

use App\Models\Programa;
use App\Models\Subprograma;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SubprogramsByProgramChart extends ChartWidget
{
    //protected static ?string $heading = 'Chart';
    protected static bool $isLazy = false;
    protected static ?string $heading = 'Subprogramas por Programa';
    protected static ?string $pollingInterval = '30s'; // Atualização a cada 10 segundos


    
    protected function getData(): array
    {
        // Consultar o número total de subprogramas por programa usando Trend
        $programas = Programa::with('subprograma')->get();

        // Agrupar e contar subprogramas por programa
        $data = $programas->map(function ($programa) {
            return [
                'nome' => $programa->nome,
                'subprograma_count' => $programa->subprograma()->count(),
            ];
        });

        $labels = $data->pluck('nome')->toArray();
        $subprogramsCount = $data->pluck('subprograma_count')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total de Subprogramas',
                    'data' => $subprogramsCount,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                    'borderWidth' => 1,
                    'barPercentage'=> 0.5,
                    
                ],
            ],
        ];
    }


    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Número de Subprogramas',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Programas',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }


    protected function getType(): string
    {
        return 'bar';
    }
}
