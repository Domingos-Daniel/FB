<?php

namespace App\Filament\Resources\ProgramaPessoaResource\Pages;

use App\Filament\Resources\ProgramaPessoaResource;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProgramaPessoa extends EditRecord
{
    protected static string $resource = ProgramaPessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\CreateAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Financiamento Actualizado')
            ->body('O financiamento foi editado com sucesso.')
            ->actions([
                Action::make('view')
                    ->label('Visualizar')
                    ->button()
                    ->url(route('filament.admin.resources.programa-pessoas.index'))
            ])
            ->sendToDatabase(\auth()->user());
    }
}
