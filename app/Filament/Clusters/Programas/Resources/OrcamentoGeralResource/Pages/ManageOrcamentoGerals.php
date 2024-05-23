<?php

namespace App\Filament\Clusters\Programas\Resources\OrcamentoGeralResource\Pages;

use App\Filament\Clusters\Programas\Resources\OrcamentoGeralResource;
use App\Models\OrcamentoGeral;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOrcamentoGerals extends ManageRecords
{
    protected static string $resource = OrcamentoGeralResource::class;
    
    protected function getHeaderActions(): array
    {
    
        $isDisabled = false;
        $currentYear = now()->format('Y');
        $existingRecord = OrcamentoGeral::whereYear('created_at', $currentYear)->first();

        if ($existingRecord) {
            // Prevent the creation of a new record
            $isDisabled = true;
        }
        return [
            Actions\CreateAction::make()
                ->label(
                    fn () => $isDisabled ? 'Orcamento já criado' : 'Criar Orçamento Geral ('.now()->format('Y').')',
                
                )
                ->hidden($isDisabled)
                ->color('primary'),
        ];
    }

}
