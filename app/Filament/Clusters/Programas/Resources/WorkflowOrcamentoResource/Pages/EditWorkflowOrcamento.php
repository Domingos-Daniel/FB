<?php

namespace App\Filament\Clusters\Programas\Resources\WorkflowOrcamentoResource\Pages;

use App\Filament\Clusters\Programas\Resources\WorkflowOrcamentoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkflowOrcamento extends EditRecord
{
    protected static string $resource = WorkflowOrcamentoResource::class;
    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
