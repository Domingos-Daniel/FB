<?php

namespace App\Filament\Clusters\Workflow\Resources\WorkflowItemResource\Pages;

use App\Filament\Clusters\Workflow\Resources\WorkflowItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkflowItems extends ListRecords
{
    protected static string $resource = WorkflowItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
