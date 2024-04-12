<?php

namespace App\Filament\Clusters\Programas\Resources\SubprogramaResource\Pages;

use App\Filament\Clusters\Programas\Resources\SubprogramaResource;
use App\Models\gasto;
use App\Models\OrcamentoPrograma;
use App\Models\Subprograma;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSubprograma extends EditRecord
{
    protected static string $resource = SubprogramaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function afterSave(): void
    {

        try {
            $subprograma = Subprograma::findOrFail($this->record['id']);
            $programa = $subprograma->programa;

            // Obtém o ID do orçamento associado ao programa
            $orcamentoPrograma = OrcamentoPrograma::where('id_programa', $programa->id)->first();
            $id_orcamento = $orcamentoPrograma ? $orcamentoPrograma->id_orcamento : null;

            // Cria o registro de Gasto
            $gasto = new gasto();
            $gasto->id_programa = $programa->id;
            $gasto->id_subprograma = $subprograma->id;
            $gasto->id_orcamento = $id_orcamento; // Utiliza o ID do orçamento obtido
            $gasto->valor_gasto = $this->record['valor'];
            $gasto->save();

            Notification::make()
                ->title('SubPrograma Alterado com sucesso')
                ->body('O seu subprograma foi editado com sucesso ')
                ->success()
                ->sendToDatabase(\auth()->user())
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erro ao salvar')
                ->body('Erro na inserção dos dados. Por favor, tente novamente: ' . $e->getMessage())
                ->danger()
                ->sendToDatabase(\auth()->user())
                ->send();
        }
    }
}
