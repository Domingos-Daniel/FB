<?php

namespace App\Filament\Clusters\Workflow\Resources\WorkflowStageResource\Pages;

use App\Filament\Clusters\Workflow\Resources\WorkflowStageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkflowStage extends CreateRecord
{
    protected static string $resource = WorkflowStageResource::class;
}
