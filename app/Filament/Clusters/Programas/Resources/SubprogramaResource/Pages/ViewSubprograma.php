<?php

namespace App\Filament\Clusters\Programas\Resources\SubprogramaResource\Pages;

use App\Filament\Clusters\Programas\Resources\SubprogramaResource;
use App\Filament\Clusters\Programas\Resources\SubprogramaResource\Widgets\SubprogramaOverview;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewSubprograma extends ViewRecord
{
    protected static string $resource = SubprogramaResource::class;
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
            SubprogramaOverview::class,
        ];
    }

    public function updated($name)
    {
        if (Str::of($name)->contains(['mountedTableAction', 'mountedTableBulkAction'])) {
            $this->emit('updateSubprogramaOverview');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Novo Subprograma'),
            Actions\EditAction::make()->label('Editar Subprograma')
                ->icon('heroicon-o-pencil')
                ->color('gray'),
            Actions\DeleteAction::make()->label('Remover Subprograma'),

        ];
    }
}
