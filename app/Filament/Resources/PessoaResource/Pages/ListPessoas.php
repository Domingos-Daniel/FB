<?php

namespace App\Filament\Resources\PessoaResource\Pages;

use App\Filament\Resources\PessoaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPessoas extends ListRecords
{
    protected static string $resource = PessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
