<?php

namespace App\Filament\Resources\PessoaResource\Pages;

use App\Filament\Resources\PessoaResource;
use Filament\Actions;
use Filament\Forms\Components\Builder\Block;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManagePessoas extends ManageRecords
{
    protected static string $resource = PessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Beneficiário Actualizado')
            ->body('O beneficiário foi editado com sucesso.')
            ->actions([
                Action::make('view')
                    ->label('Visualizar')
                    ->button()
                    ->url(route('filament.admin.resources.pessoas.index'))
            ])
            ->sendToDatabase(\auth()->user());
    }
}
