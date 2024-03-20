<?php

namespace App\Filament\Clusters\Programas\Resources\OrcamentoprogramaResource\Pages;

use App\Filament\Clusters\Programas\Resources\OrcamentoprogramaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrcamentoprograma extends ViewRecord
{
    protected static string $resource = OrcamentoprogramaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
