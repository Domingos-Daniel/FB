<?php

namespace App\Filament\Clusters\Workflow\Resources\WorkflowStageResource\Pages;

use App\Filament\Clusters\Workflow\Resources\WorkflowStageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkflowStage extends EditRecord
{
    protected static string $resource = WorkflowStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
