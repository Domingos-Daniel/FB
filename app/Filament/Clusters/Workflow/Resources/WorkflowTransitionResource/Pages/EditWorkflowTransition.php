<?php

namespace App\Filament\Clusters\Workflow\Resources\WorkflowTransitionResource\Pages;

use App\Filament\Clusters\Workflow\Resources\WorkflowTransitionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkflowTransition extends EditRecord
{
    protected static string $resource = WorkflowTransitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
