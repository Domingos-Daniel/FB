<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Actions\Action as B;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static  string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }

 

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Utilizador Actualizado')
            ->body('O utilizador foi editado com sucesso.')
            ->actions([
                B::make('view')
                    ->label('Visualizar')
                    ->button()
                    ->url(route('filament.admin.resources.users.index')),
            ])
            ->sendToDatabase(\auth()->user());
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
