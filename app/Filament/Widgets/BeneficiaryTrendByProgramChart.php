<?php

namespace App\Filament\Widgets;

use App\Models\Programa;
use App\Models\SubprogramaPessoa;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class BeneficiaryTrendByProgramChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'beneficiaryTrendByProgramChart';
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Evolução do Número de Beneficiários ao Longo do Tempo por Programa';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $programs = Programa::all();
        $series = [];

        foreach ($programs as $program) {
            $data = SubprogramaPessoa::where('id_programa', $program->id)
                                     ->select(\DB::raw('DATE(data_inicio) as date'), \DB::raw('count(*) as count'))
                                     ->groupBy('date')
                                     ->pluck('count', 'date')
                                     ->toArray();

            $series[] = [
                'name' => $program->nome,
                'data' => array_values($data),
            ];
        }

        $allDates = SubprogramaPessoa::select(\DB::raw('DATE(data_inicio) as date'))
                                     ->distinct()
                                     ->pluck('date')
                                     ->toArray();

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $allDates,
            ],
        ];
    }
}
