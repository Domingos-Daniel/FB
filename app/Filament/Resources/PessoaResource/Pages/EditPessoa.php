<?php

namespace App\Filament\Resources\PessoaResource\Pages;

use App\Filament\Resources\PessoaResource;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPessoa extends EditRecord
{
    protected static string $resource = PessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Beneficiario Editado')
            ->body('O Beneficiario foi editado com sucesso.')
            ->actions([
                Action::make('view')
                    ->label('Visualizar')
                    ->button()
                    ->url(route('filament.admin.resources.pessoas.index'))
            ])
            ->sendToDatabase(\auth()->user());
    }
}
