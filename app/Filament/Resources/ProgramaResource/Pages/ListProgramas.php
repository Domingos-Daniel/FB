<?php

namespace App\Filament\Resources\ProgramaResource\Pages;

use App\Filament\Resources\ProgramaResource;
use App\Filament\Resources\ProgramaResource\Widgets\ProgramaOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListProgramas extends ListRecords
{
    protected static string $resource = ProgramaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
