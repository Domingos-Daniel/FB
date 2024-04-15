<?php

namespace App\Filament\Clusters\Workflow\Resources\WorkflowItemResource\Pages;

use App\Filament\Clusters\Workflow\Resources\WorkflowItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkflowItem extends CreateRecord
{
    protected static string $resource = WorkflowItemResource::class;
}
