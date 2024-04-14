<?php

namespace App\Filament\Resources\SubprogramaPessoaResource\Pages;

use App\Filament\Resources\SubprogramaPessoaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubprogramaPessoa extends ViewRecord
{
    protected static string $resource = SubprogramaPessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
