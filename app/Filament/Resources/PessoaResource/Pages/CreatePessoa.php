<?php

namespace App\Filament\Resources\PessoaResource\Pages;

use App\Filament\Resources\PessoaResource;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePessoa extends CreateRecord
{
    protected static string $resource = PessoaResource::class;

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Beneficiario Adicionado')
            ->body('O Beneficiario foi criado com sucesso.')
            ->actions([
                Action::make('view')
                    ->label('Visualizar')
                    ->button()
                    ->url(route('filament.admin.resources.pessoas.index'))
            ])
            ->sendToDatabase(\auth()->user());
    }
}


