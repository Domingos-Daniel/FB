<?php

namespace App\Filament\Clusters\Programas\Resources\SubprogramaResource\Pages;

use App\Filament\Clusters\Programas\Resources\SubprogramaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubprograma extends ViewRecord
{
    protected static string $resource = SubprogramaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Novo Subprograma'),
            Actions\EditAction::make()->label('Editar Subprograma'),
            Actions\DeleteAction::make()->label('Remover Subprograma'),

        ];
    }
}
