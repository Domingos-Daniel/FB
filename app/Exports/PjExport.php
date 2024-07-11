<?php

namespace App\Exports;

use App\Models\Pj;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PjExport implements FromQuery, WithMapping, WithStyles, WithCustomStartCell
{
    protected $sectors;
    protected $sectorColors;

    public function __construct()
    {
        $this->sectors = Pj::select('sector')->distinct()->get()->pluck('sector')->toArray();
        $this->sectorColors = [
            'FF5733', // Red
            '33FF57', // Green
            '3357FF', // Blue
            'FF33A1', // Pink
            '33FFF5', // Cyan
            'F5FF33', // Yellow
            'FF8C33', // Orange
        ];
    }

    public function query()
    {
        return Pj::query();
    }

    public function map($project): array
    {
        return [
            $project->numero_projeto,
            $project->denominacao_projeto,
            $project->financiador_projeto,
            $project->provincia,
            $project->localidade,
            $project->entidade_executante,
            $project->entidade_beneficiaria,
            $project->ano_inicio,
            $project->ano_conclusao,
            $project->situacao_atual,
            $project->custo_total_projeto,
            $project->desembolso,
            $project->execucao_fisica,
            $project->data_inauguracao_previsao,
        ];
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function styles(Worksheet $sheet)
    {
        // Define title style
        $sheet->setCellValue('A1', 'PROJECTOS DE RESPONSABILIDADE SOCIAL DA ENDIAMA/PARCEIROS DO  SUBSECTOR DOS RECURSOS MINERAIS  DE '.date('Y').' - IIº SEMESTRE '.date('Y').'');
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 17,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '228B22'],
                
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Define header style with adjusted spacing
        $sheet->getStyle('A2:N2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '228B22'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(25);

        // Manually set the headers since WithHeadings was removed
        $headers = [
            'Nº',
            'DENOMINAÇÃO DO PROJECTO',
            'FINANCIADOR DO PROJECTO',
            'PROVINCIA',
            'LOCALIDADE',
            'ENTIDADE EXECUTANTE',
            'ENTIDADE BENEFICIÁRIA',
            'ANO INÍCIO',
            'ANO CONCLUSÃO',
            'SITUAÇÃO ACTUAL',
            'CUSTO TOTAL DO PROJECTO (USD)',
            'DESPESAS (USD)',
            'EXECUÇÃO FÍSICA (%)',
            'DATA DE INAUGURAÇÃO (PREVISÃO)',
        ];

        $sheet->fromArray($headers, null, 'A2');

        $row = 3; // Starting row for data
        $colorIndex = 0;

        foreach ($this->sectors as $sector) {
            // Cycle through the colors
            $sectorColor = $this->sectorColors[$colorIndex % count($this->sectorColors)];
            $colorIndex++;

            // Set sector row
            $sheet->setCellValue('A' . $row, '' . $sector);
            $sheet->mergeCells('A' . $row . ':N' . $row);
            $sheet->getStyle('A' . $row . ':N' . $row)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_BLACK],
                    'size' => 14,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $sectorColor],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            $row++;

            // Get projects for the sector
            $projects = Pj::where('sector', $sector)->get();
            foreach ($projects as $project) {
                $sheet->fromArray($this->map($project), null, 'A' . $row);
                $row++;
            }
        }

        return [
            // Other style definitions here if necessary
        ];
    }
}
