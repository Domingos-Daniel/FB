<?php

namespace App\Filament\Clusters\Programas\Resources\OrcamentoprogramaResource\Pages;

use App\Filament\Clusters\Programas\Resources\OrcamentoprogramaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrcamentoprograma extends CreateRecord
{
    protected static string $resource = OrcamentoprogramaResource::class;
    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
