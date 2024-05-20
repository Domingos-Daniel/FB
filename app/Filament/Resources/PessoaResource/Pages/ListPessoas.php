<?php

namespace App\Filament\Resources\PessoaResource\Pages;

use App\Filament\Resources\PessoaResource;
use App\Filament\Resources\PessoaResource\Widgets\PessoaOverview as WidgetsPessoaOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListPessoas extends ListRecords
{
    protected static string $resource = PessoaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WidgetsPessoaOverview::class,
        ];
    }

    public function updated($name)
    {
        if (Str::of($name)->contains(['mountedTableAction', 'mountedTableBulkAction'])) {
            $this->emit('updatePessoaOverview');
        }
    }
}
