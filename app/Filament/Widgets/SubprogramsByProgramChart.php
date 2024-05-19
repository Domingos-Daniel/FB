<?php

namespace App\Filament\Widgets;

use App\Models\Programa;
use App\Models\Subprograma;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SubprogramsByProgramChart extends ChartWidget
{
    //protected static ?string $heading = 'Chart';
    protected static bool $isLazy = false;
    protected static ?string $heading = 'Subprogramas por Programa';
    protected static ?string $pollingInterval = '30s'; // Atualização a cada 30 segundos


    
    protected function getData(): array
    {
        
        // Consultar o número de subprogramas por programa
        $programs = Programa::withCount('subprograma')->get();

        $labels = $programs->pluck('nome')->toArray();
        $data = $programs->pluck('subprograma_count')->toArray();
        $totalSubprograms = Subprograma::count();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Número de Subprogramas',
                    'data' => $data,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                    'borderWidth' => 1,
                    'barPercentage' => 0.5,
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
