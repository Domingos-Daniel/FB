<?php

namespace App\Filament\Exports;

use App\Models\Programa;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProgramaExporter extends Exporter
{
    protected static ?string $model = Programa::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('nome'),
            ExportColumn::make('descricao'),
            ExportColumn::make('area_foco'),
            ExportColumn::make('publico_alvo'),
            ExportColumn::make('objetivo'),
            ExportColumn::make('metas'),
            ExportColumn::make('orcamento'),
            ExportColumn::make('data_inicio'),
            ExportColumn::make('data_fim'),
            ExportColumn::make('responsavel'),
            ExportColumn::make('status'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A exportação de Porgrama(s) foi concluida com sucesso e  ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' Falha na exportação.';
        }

        return $body;
    }
}
