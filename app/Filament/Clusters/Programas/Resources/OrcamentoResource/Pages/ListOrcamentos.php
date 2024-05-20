<?php

namespace App\Filament\Clusters\Programas\Resources\OrcamentoResource\Pages;

use App\Filament\Clusters\Programas\Resources\OrcamentoResource;
use App\Filament\Clusters\Programas\Resources\OrcamentoResource\Widgets\OrcamentoOverview;
use App\Models\Orcamento;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListOrcamentos extends ListRecords
{
    protected static string $resource = OrcamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrcamentoOverview::class,
        ];
    }

    public function updated($name)
    {
        if (Str::of($name)->contains(['mountedTableAction', 'mountedTableBulkAction'])) {
            $this->emit('updateOrcamentoOverview');
        }
    }
}
