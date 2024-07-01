<?php

namespace App\Filament\Widgets;

use App\Models\Programa;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ProgramsByFocusAreaChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'programsByFocusAreaChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected int | string | array $columnSpan = 'full';
    protected static bool $isLazy = false;
    protected static ?string $heading = 'Programas por Area de Foco';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $data = Programa::select(\DB::raw('area_foco, count(*) as count'))
                        ->groupBy('area_foco')
                        ->pluck('count', 'area_foco')
                        ->toArray();

                        // Decodifica os caracteres unicode para garantir que os valores sejam tratados corretamente
// Convertendo para JSON e depois decodificando
// Converte a string para UTF-8 para garantir que caracteres especiais sejam interpretados corretamente
$array_decodificado = array_map(function($item) {
    return json_decode('"' . $item . '"');
}, $data);


        return [ 
            'chart' => [
                'type' => 'bar',
                'height' => 500,
                'width' => '100%',
            ],
            'zoom' => [
                'enabled' => true,
                'autoScaleYaxis' => true
            ],
            'series' => [
                [
                    'name' => 'Programas',
                    'data' => array_values($data),
                ],
            ],
            'xaxis' => [
                'categories' => array_keys($array_decodificado),
            ],
            'colors' => ['#f59e0b'],

        ];
    }
}
