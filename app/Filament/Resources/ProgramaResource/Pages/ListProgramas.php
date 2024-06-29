<?php

namespace App\Filament\Resources\ProgramaResource\Pages;

use App\Exports\CombinedExport;
use App\Exports\ProjectsExport;
use App\Filament\Resources\ProgramaResource;
use App\Filament\Resources\ProgramaResource\Widgets\ProgramaOverview;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ListProgramas extends ListRecords
{
    protected static string $resource = ProgramaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('exporter')
                ->color('info')
                ->icon('heroicon-o-document-text')
                ->size('xl')
                ->label('Exportar Planilha Modelo Geral')
                ->action(function () {
                    return Excel::download(new ProjectsExport, 'Planilha modelos Fundação Brilhante de ' . date('d-m-Y H:i:s') . '.xlsx');
                })
        ];
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProgramaOverview::class,
        ];
    }

    public function updated($name)
    {
        if (Str::of($name)->contains(['mountedTableAction', 'mountedTableBulkAction'])) {
            $this->emit('updateProgramaOverview');
        }
    }
}
