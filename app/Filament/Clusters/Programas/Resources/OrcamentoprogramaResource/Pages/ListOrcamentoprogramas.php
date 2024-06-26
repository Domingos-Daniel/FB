<?php

namespace App\Filament\Clusters\Programas\Resources\OrcamentoprogramaResource\Pages;

use App\Filament\Clusters\Programas\Resources\OrcamentoprogramaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrcamentoprogramas extends ListRecords
{
    protected static string $resource = OrcamentoprogramaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Atribuir Orçamento'),
        ];
    }
}
