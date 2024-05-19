<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Programa;

class SubprogramsByProgramPieChart extends ChartWidget
{
    protected static ?string $heading = 'Distribuição de Subprogramas por Programa';
    protected static bool $isLazy = false;

    protected function getType(): string
    {
        return 'doughnut'; // Tipo do gráfico
    }

    protected function getData(): array
    {
        // Consultar programas e seus subprogramas
        $programs = Programa::with('subprograma')->get();

        $datasets = [];

        foreach ($programs as $program) {
            $subprogramsCount = $program->subprograma->count();

            if ($subprogramsCount > 0) {
                $datasets[$program->nome] = $subprogramsCount;
            }
        }

        // Ordenar os dados pelo número de subprogramas em ordem decrescente
        arsort($datasets);

        // Separar os 5 principais programas e agrupar o restante em "Outros"
        $topPrograms = array_slice($datasets, 0, 5);
        $otherProgramsCount = array_sum(array_slice($datasets, 5));
        $topPrograms['Outros'] = $otherProgramsCount;

        // Preparar os dados para o gráfico
        $labels = array_keys($topPrograms);
        $data = array_values($topPrograms);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Subprogramas',
                    'data' => $data,
                    'backgroundColor' => $this->generateRandomColors(count($labels)),
                ],
            ],
        ];
    }

    protected function generateRandomColors(int $count): array
    {
        $colors = [];

        for ($i = 0; $i < $count; $i++) {
            $colors[] = '#' . substr(md5($i), 0, 6); // Gerar cores baseadas em um índice único
        }

        return $colors;
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }
}
