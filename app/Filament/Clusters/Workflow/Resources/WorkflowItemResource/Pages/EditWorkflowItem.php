<?php

namespace App\Filament\Clusters\Workflow\Resources\WorkflowItemResource\Pages;

use App\Filament\Clusters\Workflow\Resources\WorkflowItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkflowItem extends EditRecord
{
    protected static string $resource = WorkflowItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
