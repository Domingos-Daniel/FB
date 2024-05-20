<?php

namespace App\Filament\Clusters\Programas\Resources\SubprogramaResource\Pages;

use App\Filament\Clusters\Programas\Resources\SubprogramaResource;
use App\Filament\Clusters\Programas\Resources\SubprogramaResource\Widgets\SubprogramaOverview;
use App\Models\gasto;
use App\Models\Orcamento;
use App\Models\OrcamentoPrograma;
use App\Models\Subprograma;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateSubprograma extends CreateRecord
{
    protected static string $resource = SubprogramaResource::class;

    protected function mutateDataBeforeCreate(array $data):array{
        $data['user_id'] = auth()->user()->id;
     
        return $data;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SubprogramaOverview::class,
        ];
    }

    public function updated($name)
    {
        if (Str::of($name)->contains(['mountedTableAction', 'mountedTableBulkAction'])) {
            $this->emit('updateSubprogramaOverview');
        }
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function afterCreate(): void
{

    try {
        $subprograma = Subprograma::findOrFail($this->record['id']);
        $programa = $subprograma->programa;

        // Obtém o ID do orçamento associado ao programa
        $orcamentoPrograma = OrcamentoPrograma::where('id_programa', $programa->id)->first();
        $id_orcamento = $orcamentoPrograma ? $orcamentoPrograma->id_orcamento : null;

        // Cria o registro de Gasto
        $gasto = new Gasto();
        $gasto->id_programa = $programa->id;
        $gasto->id_subprograma = $subprograma->id;
        $gasto->id_orcamento = $id_orcamento; // Utiliza o ID do orçamento obtido
        $gasto->valor_gasto = $this->record['valor'];
        $gasto->save();

        Notification::make()
            ->title('SubPrograma Adicionado com sucesso')
            ->body('O seu subprograma foi adicionado com sucesso ')
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