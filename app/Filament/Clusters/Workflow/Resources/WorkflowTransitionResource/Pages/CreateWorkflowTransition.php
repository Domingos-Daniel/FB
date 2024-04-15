<?php

namespace App\Filament\Clusters\Workflow\Resources\WorkflowTransitionResource\Pages;

use App\Filament\Clusters\Workflow\Resources\WorkflowTransitionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkflowTransition extends CreateRecord
{
    protected static string $resource = WorkflowTransitionResource::class;
}
