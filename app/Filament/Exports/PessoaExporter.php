<?php

namespace App\Filament\Exports;

use App\Models\Pessoa;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PessoaExporter extends Exporter
{
    protected static ?string $model = Pessoa::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('nome'),
            ExportColumn::make('email'),
            ExportColumn::make('bi'),
            ExportColumn::make('data_nascimento'),
            ExportColumn::make('genero'),
            ExportColumn::make('grau_academico'),
            ExportColumn::make('morada'),
            ExportColumn::make('telefone'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A exportação de Beneficiário(s) foi concluida com sucesso e  ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' foram exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' Falha na exportação.';
        }

        return $body;
    }
}
