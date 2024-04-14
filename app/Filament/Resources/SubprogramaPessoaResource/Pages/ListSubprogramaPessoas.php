<?php

namespace App\Filament\Resources\SubprogramaPessoaResource\Pages;

use App\Filament\Resources\SubprogramaPessoaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubprogramaPessoas extends ListRecords
{
    protected static string $resource = SubprogramaPessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
