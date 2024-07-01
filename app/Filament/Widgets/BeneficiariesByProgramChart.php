<?php

namespace App\Filament\Widgets;

use App\Models\Programa;
use App\Models\SubprogramaPessoa;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class BeneficiariesByProgramChart extends ApexChartWidget
{
    
    protected static bool $isLazy = false;
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'beneficiariesByProgramChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Beneficiários por Programa';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $data = SubprogramaPessoa::select('id_programa', \DB::raw('count(*) as count'))
                                 ->groupBy('id_programa')
                                 ->pluck('count', 'id_programa')
                                 ->toArray();

        $programNames = Programa::whereIn('id', array_keys($data))
                                ->pluck('nome', 'id')
                                ->toArray();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => array_values($data),
            'labels' => array_values($programNames),
        ];
    }
}
