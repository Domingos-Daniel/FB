<?php

namespace App\Filament\Clusters\Programas\Resources\WorkflowOrcamentoResource\Pages;

use App\Filament\Clusters\Programas\Resources\WorkflowOrcamentoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkflowOrcamento extends CreateRecord
{
    protected static string $resource = WorkflowOrcamentoResource::class;
    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    
}
