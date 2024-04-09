<?php

namespace App\Filament\Clusters\Programas\Resources\SubprogramaResource\Pages;

use App\Filament\Clusters\Programas\Resources\SubprogramaResource;
use App\Models\gasto;
use App\Models\Orcamento;
use App\Models\OrcamentoPrograma;
use App\Models\Subprograma;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSubprograma extends CreateRecord
{
    protected static string $resource = SubprogramaResource::class;

    protected function mutateDataBeforeCreate(array $data):array{
        $data['user_id'] = auth()->user()->id;
     
        return $data;
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

}