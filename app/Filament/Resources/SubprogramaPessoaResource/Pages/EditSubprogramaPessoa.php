<?php

namespace App\Filament\Resources\SubprogramaPessoaResource\Pages;

use App\Filament\Resources\SubprogramaPessoaResource;
use App\Models\SubprogramaPessoa;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSubprogramaPessoa extends EditRecord
{
    protected static string $resource = SubprogramaPessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $id_programa = $this->data['id_programa'] ?? null;
        $id_subprograma = $this->data['id_subprograma'] ?? null;
        $id_pessoa = $this->data['id_pessoa'] ?? null;

        // Verificar se os IDs estão definidos
        if ($id_programa === null || $id_subprograma === null || $id_pessoa === null) {
            // Se algum dos IDs não estiver definido, interrompa o processo de criação
            $this->halt();
            return;
        }

        // Verificar se já existe um registro com as mesmas condições
        $existingRecord = SubprogramaPessoa::where('id_programa', $id_programa)
            ->where('id_subprograma', $id_subprograma)
            ->where('id_pessoa', $id_pessoa)
            ->exists();

        // Se já existir um registro com as mesmas condições, interrompa o processo de criação
        if ($existingRecord) {
            // Emitir uma notificação de erro
            Notification::make()
                ->danger()
                ->duration(5000)
                ->title('Erro ao atualizar registro')
                ->body('Já existe um registro com os mesmos registos de programa, subprograma e beneficiario.')
                ->send();

            // Interromper o processo de criação
            $this->halt();
        } else {
            Notification::make()
                ->success()
                ->duration(5000)
                ->title('Financiamento Editado')
                ->body('O financiamento foi editado com sucesso!')
                ->sendToDatabase(\auth()->user())
                ->send();
        }
    }
}
