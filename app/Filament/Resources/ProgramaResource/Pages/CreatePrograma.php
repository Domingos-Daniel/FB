<?php

namespace App\Filament\Resources\ProgramaResource\Pages;

use App\Filament\Resources\ProgramaResource;
use App\Models\Programa;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePrograma extends CreateRecord
{
    protected static string $resource = ProgramaResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    } 

    protected function beforeCreate(): void
    {
        $programa = $this->data['nome'] ?? null;
        
        // Verificar se já existe um registro com as mesmas condições
        $existingRecord = Programa::where('nome', $programa)
            ->exists();

        // Se já existir um registro com as mesmas condições, interrompa o processo de criação
        if ($existingRecord) {
            // Emitir uma notificação de erro
            Notification::make()
                ->danger()
                ->duration(5000) 
                ->title('Erro ao criar registro')
                ->body('Já existe um registro com os mesmos registos de programa.')
                ->send();

            // Interromper o processo de criação
            $this->halt();
        }else{
            // Emitir uma notificação de sucesso
            Notification::make()
                ->success()
                ->duration(5000)
                ->title('Programa criado')
                ->body('Programa criado com sucesso')
                ->sendToDatabase(\auth()->user())
                ->send();
        }
    }
}
