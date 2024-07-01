<?php

namespace App\Filament\Widgets;

use App\Models\Pessoa;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class GenderDistributionChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'genderDistributionChart';

    protected static ?string $heading = 'Distribuição de Gênero dos Beneficiários';

    /**
     * Widget Title
     *
     * @var string|null
     */

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected static bool $isLazy = false;
    protected function getOptions(): array
    {
        $data = Pessoa::select('genero', \DB::raw('count(*) as count'))
                      ->groupBy('genero')
                      ->pluck('count', 'genero')
                      ->toArray();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
                'width' => '100%',
                'toolbar' => [
                    'show' => true
                ],
                'zoom' => [
                    'enabled' => true
                ]
            ],
            'series' => array_values($data),
            'labels' => array_keys($data),
        ];
    }
}
