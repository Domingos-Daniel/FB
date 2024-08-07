<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Programa;
use App\Models\Subprograma;
use App\Models\SubprogramaPessoa;

class ProjectsExport implements FromCollection, WithMapping, WithStyles, ShouldAutoSize, WithCustomStartCell
{
    public function collection()
    {
        return Programa::with(['orcamentos'])->get();
    }

    public function map($programa): array
    {
        $orcamento = $programa->orcamentos->first();
        // Inicialize a contagem total de usuários associados
        $totalUserCount = 0;

        // Obtenha todos os subprogramas para o programa atual
        $subprogramasIds = Subprograma::where('id_programa', $programa->id)->pluck('id');

        // Se houver subprogramas, obtenha a contagem total de usuários associados a esses subprogramas
        if ($subprogramasIds->isNotEmpty()) {
            $totalUserCount = SubprogramaPessoa::whereIn('id_subprograma', $subprogramasIds)->count();
        }

        // Format area_foco as a string if it's an array
        $areaFocoFormatted = is_array($programa->area_foco) ? implode(', ', $programa->area_foco) : $programa->area_foco;
        // Calculate the status based on the difference in dates
        $status = 'Em andamento';
        if ($programa->data_fim) {
            $differenceInDays = $programa->data_fim->diffInDays(now());
            if ($differenceInDays >= 90) {
                $status = 'Finalizando';
            } elseif ($differenceInDays >= 50) {
                $status = 'Em andamento';
            } else {
                $status = 'Expirado';
            }
        }

        return [
            $programa->id,
            $programa->nome,
            $programa->objetivo,
            'area_foco' => $areaFocoFormatted,
            'PROVÍNCIA' => 'Luanda',
            'LOCALIDADE' => 'Luanda',
            $programa->created_at->format('d/m/Y'),
            $programa->updated_at->format('d/m/Y'),
            $status,
            $orcamento ? $orcamento->valor : 0,
            'desembolso' => '0.00',
            $orcamento ? $orcamento->execucao_fisica : 0,
            $programa->created_at->addYear()->format('d/m/Y'), // Add one year to the created_at date
            $totalUserCount,
            'sector' => $areaFocoFormatted
        ];
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function styles(Worksheet $sheet)
    {
        // Título na célula A2
        $sheet->setCellValue('A3', 'PROJECTOS DE RESPONSABILIDADE SOCIAL DA ENDIAMA/FBRILHANTE SUBSECTOR DOS RECURSOS MINERAIS '.date('Y').' - Iº SEMESTRE ANO_ACTUAL');
        $sheet->mergeCells('A3:O3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['name' => 'Arial', 'bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'D3D3D3'], // Fundo meio cinza
            ],
        ]);

        // Cabeçalhos na linha 5
        $headers = [
            'Nº', 'DENOMINAÇÃO DO PROJECTO', 'OBJECTIVO', 'ÁREA DE INTERVENÇÃO', 'PROVÍNCIA', 'LOCALIDADE',
            'ANO INÍCIO', 'ANO DE CONCLUSÃO', 'SITUAÇÃO ACTUAL (Estado)', 'CUSTO TOTAL DO PROJECTO (USD)',
            'DESEMBOLSO (USD)', 'EXECUÇÃO FÍSICA (%)', 'DATA DE INAUGURAÇÃO (PREVISÃO)', 'IMPACTO NUMÉRICO', 'SECTOR'
        ];

        foreach ($headers as $key => $header) {
            $sheet->setCellValueByColumnAndRow($key + 1, 5, $header);
        }

        $headerStyle = [
            'font' => ['name' => 'Cambria', 'bold' => true, 'size' => 12],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,

            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'ADD8E6'], // Fundo meio azul
            ],
            'height' => 25,
            'rowHeight' => 25,
        ];

        $sheet->getStyle('A5:O5')->applyFromArray($headerStyle);

        // Estilo do corpo
        $bodyStyle = [
            'font' => ['name' => 'Cambria'],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A6:O' . ($sheet->getHighestRow() + 5))->applyFromArray($bodyStyle);
        // Ajustar largura das colunas automaticamente
        foreach (range('A', 'O') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Rodape na linha da última célula
        $footerStyle = [
            'font' => ['name' => 'Cambria', 'size' => 10, 'color' => ['argb' => '808080']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT],
        ];

        $sheet->getStyle('A' . ($sheet->getHighestRow() + 6) . ':O' . ($sheet->getHighestRow() + 6))
            ->applyFromArray($footerStyle);

        $sheet->setCellValue('A' . ($sheet->getHighestRow() + 6), 'Processado pelo SIGP em ' . now()->format('d/m/Y H:i:s'));

        return [
            2 => ['font' => ['bold' => true, 'size' => 14]], // Título
            5 => ['font' => ['bold' => true]], // Cabeçalho
        ];
    }
}
