<?php

namespace App\Filament\Resources\ProgramaPessoaResource\Pages;

use App\Filament\Resources\ProgramaPessoaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgramaPessoa extends EditRecord
{
    protected static string $resource = ProgramaPessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
