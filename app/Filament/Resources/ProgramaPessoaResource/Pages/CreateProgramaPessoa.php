<?php

namespace App\Filament\Resources\ProgramaPessoaResource\Pages;

use App\Filament\Resources\ProgramaPessoaResource;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramaPessoa extends CreateRecord
{
    protected static string $resource = ProgramaPessoaResource::class;
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Financiamento Adicionado')
            ->body('O financiamento foi adicionado com sucesso.')
            ->actions([
                Action::make('view')
                    ->label('Visualizar')
                    ->button()
                    ->url(route('filament.admin.resources.programa-pessoas.index'))
            ])
            ->sendToDatabase(\auth()->user());
    }
}
