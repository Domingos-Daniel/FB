<?php

namespace App\Filament\Exports;

use App\Models\SubprogramaPessoa;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SubprogramaPessoaExporter extends Exporter
{
    protected static ?string $model = SubprogramaPessoa::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('id_programa'),
            ExportColumn::make('id_subprograma'),
            ExportColumn::make('id_pessoa'),
            ExportColumn::make('description'),
            ExportColumn::make('data_inicio'),
            ExportColumn::make('data_fim'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your subprograma pessoa export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
