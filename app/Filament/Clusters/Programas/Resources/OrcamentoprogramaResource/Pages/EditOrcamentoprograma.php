<?php

namespace App\Filament\Clusters\Programas\Resources\OrcamentoprogramaResource\Pages;

use App\Filament\Clusters\Programas\Resources\OrcamentoprogramaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrcamentoprograma extends EditRecord
{
    protected static string $resource = OrcamentoprogramaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
