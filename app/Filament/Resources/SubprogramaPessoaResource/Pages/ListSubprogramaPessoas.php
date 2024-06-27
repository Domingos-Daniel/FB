<?php

namespace App\Filament\Resources\SubprogramaPessoaResource\Pages;

use App\Filament\Clusters\Programas\Resources\SubprogramaResource\Widgets\SubprogramaOverview;
use App\Filament\Resources\SubprogramaPessoaResource;
use App\Filament\Resources\SubprogramaPessoaResource\Widgets\SubprogramaPessoaOverview;
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

    protected function getHeaderWidgets(): array
    {
        return [
            SubprogramaPessoaOverview::class,
        ];
    }
}
