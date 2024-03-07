<?php

namespace App\Filament\Resources\ProgramaResource\Pages;

use App\Filament\Resources\ProgramaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProgramas extends ManageRecords
{
    protected static string $resource = ProgramaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
