<?php

namespace App\Filament\Resources\PessoaResource\Pages;

use App\Filament\Resources\PessoaResource;
use Filament\Actions;
use Filament\Actions\Modal\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewPessoa extends ViewRecord
{
    protected static string $resource = PessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\EditAction::make()
            ->color('info'),
            Actions\DeleteAction::make(),
        ];
    }
}
