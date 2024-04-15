<?php

namespace App\Filament\Clusters\Workflow\Resources\WorkflowItemResource\Pages;

use App\Filament\Clusters\Workflow\Resources\WorkflowItemResource;
use Filament\Resources\Pages\Page;

class ViewWorkflowItemsPage extends Page
{
    protected static string $resource = WorkflowItemResource::class;

    protected static string $view = 'filament.clusters.workflow.resources.workflow-item-resource.pages.view-workflow-items-page';
}
