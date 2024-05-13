<?php

namespace App\Filament\Clusters\Programas\Resources\PassosWorkflowResource\Pages;

use App\Filament\Clusters\Programas\Resources\PassosWorkflowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPassosWorkflows extends ListRecords
{
    protected static string $resource = PassosWorkflowResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
