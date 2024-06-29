<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Pessoa;
use App\Models\programa;
use App\Models\Orcamento;
use App\Models\Orcamentoprograma;

class CombinedExport implements FromQuery, WithMapping, WithStyles, ShouldAutoSize
{
    public function query()
    {
        return Pessoa::query()
            ->join('programas', 'pessoas.id', '=', 'programas.responsavel')
            ->join('orcamento_programas', 'programas.id', '=', 'orcamento_programas.id_programa')
            ->join('orcamentos', 'orcamento_programas.id_orcamento', '=', 'orcamentos.id')
            ->select([
                'pessoas.nome as nome_pessoa',
                'pessoas.email',
                'pessoas.telefone',
                'programas.nome as nome_programas',
                'programas.descricao as descricao_programas',
                'programas.area_foco',
                'programas.publico_alvo',
                'programas.objetivo',
                'orcamentos.valor as valor_orcamento',
                'orcamentos.descricao as descricao_orcamento',
            ]);
    }

    public function map($row): array
    {
        return [
            $row->nome_pessoa,
            $row->email,
            $row->telefone,
            $row->nome_programas,
            $row->descricao_programas,
            implode(", ", json_decode($row->area_foco, true)), // caso seja array
            implode(", ", json_decode($row->publico_alvo, true)), // caso seja array
            $row->objetivo,
            $row->valor_orcamento,
            $row->descricao_orcamento,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Título na célula A2
        $sheet->setCellValue('A2', 'Título do Relatório');
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'font-family' => 'Arial', 
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Cabeçalhos na linha 5
        $headers = [
            'Nome', 'Email', 'Telefone', 'Nome do Programas', 'Descrição do Programas', 'Área de Foco',
            'Público Alvo', 'Objetivo', 'Valor do Orçamento', 'Descrição do Orçamento'
        ];

        foreach ($headers as $key => $header) {
            $sheet->setCellValueByColumnAndRow($key + 1, 5, $header);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'padding' => [
                'top' => 10,
                'bottom' => 10,
                'left' => 10,
                'right' => 10,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => '87CEEB'], // Fundo azul claro
            ],
        ];

        $sheet->getStyle('A5:J5')->applyFromArray($headerStyle);

        // Estilo do corpo
        $bodyStyle = [
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A6:J' . ($sheet->getHighestRow() + 5))->applyFromArray($bodyStyle);

        // Ajustar largura das colunas automaticamente
        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        return [
            2 => ['font' => ['bold' => true, 'size' => 14]], // Título
            5 => ['font' => ['bold' => true]], // Cabeçalho
        ];
    }
}
