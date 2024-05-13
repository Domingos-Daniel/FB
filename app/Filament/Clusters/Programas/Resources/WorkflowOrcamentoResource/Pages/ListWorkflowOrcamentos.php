<?php

namespace App\Filament\Clusters\Programas\Resources\WorkflowOrcamentoResource\Pages;

use App\Filament\Clusters\Programas\Resources\WorkflowOrcamentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkflowOrcamentos extends ListRecords
{
    protected static string $resource = WorkflowOrcamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
