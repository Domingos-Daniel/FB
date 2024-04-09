<?php

namespace App\Filament\Clusters\Programas\Resources\SubprogramaResource\Pages;

use App\Filament\Clusters\Programas\Resources\SubprogramaResource;
use App\Models\gasto;
use App\Models\Orcamento;
use App\Models\OrcamentoPrograma;
use App\Models\Subprograma;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSubprograma extends CreateRecord
{
    protected static string $resource = SubprogramaResource::class;

    protected function mutateDataBeforeCreate(array $data):array{
        $data['user_id'] = auth()->user()->id;
     
        return $data;
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function beforeCreate(): void
{
    $id_programa = $this->data['id_programa'] ?? null; // Obtém o id do programa social selecionado do array de dados

    if ($id_programa === null) {
        // Se o id_programa não estiver definido, interrompa o processo de criação
        $this->halt();
        return;
    }

    $valor_submetido = $this->record['valor'] ?? null; // Obtém o valor submetido

    // Carregar o valor do orçamento com base no programa social selecionado
    $valor_orcamento = Orcamento::where('id', $id_programa)->value('valor');

    // Calcular a quantidade de valor gasto para este programa
    $valor_gasto = Gasto::where('id_programa', $id_programa)->sum('valor_gasto');

    // Calcular o valor disponível no orçamento
    $valor_disponivel = $valor_orcamento - $valor_gasto;

    // Verificar se o valor submetido é maior que o valor disponível no orçamento
    if ($valor_submetido > $valor_disponivel) {
        // Emitir uma notificação de erro
        Notification::make()
            ->error()
            ->title('Erro ao criar registro')
            ->message('O valor submetido é maior que o valor disponível no orçamento.')
            ->send();

        // Interromper o processo de criação
        $this->halt();
    }
}

    

    

}