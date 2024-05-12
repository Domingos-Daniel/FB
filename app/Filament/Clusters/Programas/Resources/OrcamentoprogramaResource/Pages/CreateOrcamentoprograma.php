<?php

namespace App\Filament\Clusters\Programas\Resources\OrcamentoprogramaResource\Pages;

use App\Filament\Clusters\Programas\Resources\OrcamentoprogramaResource;
use App\Models\OrcamentoPrograma;
use Filament\Actions;
use Filament\Notifications\Notification;
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

    protected function beforeCreate(): void
    {
        $id_programa = $this->data['id_programa'] ?? null;
        
        
        // Verificar se já existe um registro com as mesmas condições
        $existingRecord = OrcamentoPrograma::where('id_programa', $id_programa)
            ->exists();

        // Se já existir um registro com as mesmas condições, interrompa o processo de criação
        if ($existingRecord) {
            // Emitir uma notificação de erro
            Notification::make()
                ->danger()
                ->duration(5000)
                ->title('Erro ao atribuir Orcamento')
                ->body('O programa selecionado, ja possui um orcamento')
                ->send();

            // Interromper o processo de criação
            $this->halt();
        } 
    }
}
