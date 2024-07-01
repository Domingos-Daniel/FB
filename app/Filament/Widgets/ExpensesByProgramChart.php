<?php

namespace App\Filament\Widgets;

use App\Models\gasto;
use App\Models\Programa;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ExpensesByProgramChart extends ApexChartWidget
{
    
    protected static ?int $sort = 0;
    protected static ?string $pollingInterval = '30s';
    protected static bool $isLazy = false;
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'expensesByProgramChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
     protected static ?string $heading = 'Despesas por Programa';
    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $data = gasto::select('id_programa', \DB::raw('SUM(valor_gasto) as total_gasto'))
                     ->groupBy('id_programa')
                     ->pluck('total_gasto', 'id_programa')
                     ->toArray();

        $programNames = Programa::whereIn('id', array_keys($data))
                                ->pluck('nome', 'id')
                                ->toArray();
        // Calculating the total
        $total = array_sum($data);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'width' => '100%',
            ],
            'series' => [
                [
                    'name' => 'Despesas',
                    'data' => array_values($data),

                ],
            ],
            'xaxis' => [
                'categories' => array_values($programNames),

            ],
            
            'colors' => ['#f59e0b'],
            'total' => $total,
        ];
    }
}
