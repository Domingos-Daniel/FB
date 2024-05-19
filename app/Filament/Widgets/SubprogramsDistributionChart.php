<?php

namespace App\Filament\Widgets;

use App\Models\Programa;
use Filament\Widgets\ChartWidget;

class SubprogramsDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Grafico de Distribuição de subprogramas';
    protected static ?string $subHeading = 'Subprogramas';
    protected static ?string $maxHeight = '270px';
    protected static bool $isLazy = false;

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $subprograms = Programa::with('subprograma')->get();
        $subprogramas = [];
        foreach ($subprograms as $subprogram) {
            $subprogramas[] = $subprogram->subprograma()->count();
        }

        return [
            'labels' => $subprograms->pluck('nome')->toArray(),
            'datasets' => [
                [
                    'label' => 'Subprogramas',
                    'data' => $subprogramas,
                    'yAxisID' => false,
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        '#23CE6B',
                        '#AF49AF',
                        '#2F6EB6',
                        '#D10F3A',
                    ],
                    'borderColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        '#23CE6B',
                        '#AF49AF',
                        '#2F6EB6',
                        '#D10F3A',
                    ],
                    'hoverBackgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        '#23CE6B',
                        '#AF49AF',
                        '#2F6EB6',
                        '#D10F3A',
                    ],
                    'hoverOffset' => 4,
                    'borderWidth' => 1,
                    'borderRadius' => 5,
                    'borderSkipped' => 'bottom',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#FF6384',
                    'pointBorderColor' => '#FF6384',
                    'pointHoverBackgroundColor' => '#FF6384',
                ],
            ],
           
        ];
    }

    protected function getOptions(): array
{
    return [
        'plugins' => [
            'legend' => [
                'display' => true,
                'position' => 'top',
            ],
            'tooltip' => [
                'enabled' => true,
            ],
        ],
        'scales' => [
            'y' => [
                'display' => false, // Oculta o eixo Y
            ],
            'x' => [   
                'display' => false, // Oculta o eixo X
            ],
        ],
        'responsive' => true,
        'maintainAspectRatio' => false,
        'responsiveAnimationDuration' => 0,
        'animation' => [
            'duration' => 0,
        ],


    ];
}



}
