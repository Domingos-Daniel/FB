<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Utilizador Criado')
            ->body('O utilizador foi criado com sucesso.')
            ->actions([
                Action::make('view')
                    ->label('Visualizar')
                    ->button()
                    ->url(route('filament.admin.resources.users.index'))
            ])
            ->sendToDatabase(\auth()->user());
    }

    

}
