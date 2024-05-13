<?php

namespace App\Filament\Clusters\Programas\Resources\PassosWorkflowResource\Pages;

use App\Filament\Clusters\Programas\Resources\PassosWorkflowResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPassosWorkflow extends EditRecord
{
    protected static string $resource = PassosWorkflowResource::class;
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
