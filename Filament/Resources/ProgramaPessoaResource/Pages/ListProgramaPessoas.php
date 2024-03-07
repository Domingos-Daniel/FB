<?php

namespace App\Filament\Resources\ProgramaPessoaResource\Pages;

use App\Filament\Resources\ProgramaPessoaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgramaPessoas extends ListRecords
{
    protected static string $resource = ProgramaPessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
