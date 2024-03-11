<?php

namespace App\Filament\Exports;

use App\Models\ProgramaPessoa;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProgramaPessoaExporter extends Exporter 
{
    protected static ?string $model = ProgramaPessoa::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('programa_id'),
            ExportColumn::make('pessoa_id'),
            ExportColumn::make('status'),
            ExportColumn::make('data_inicio'),
            ExportColumn::make('data_fim'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A exportação de Financiamento(s) foi concluida com sucesso e ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' Falha na exportação.';
        }

        return $body;
    }
}
