<?php

namespace App\Filament\Clusters\Workflow\Resources\WorkflowStageResource\Pages;

use App\Filament\Clusters\Workflow\Resources\WorkflowStageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkflowStages extends ListRecords
{
    protected static string $resource = WorkflowStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
           Actions\CreateAction::make(),
        ];
    }
}
