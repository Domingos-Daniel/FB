<?php

namespace App\Filament\Clusters\Programas\Resources\OrcamentoResource\Pages;

use App\Filament\Clusters\Programas\Resources\OrcamentoResource;
use App\Models\Orcamento;
use App\Models\WorkflowItem;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateOrcamento extends CreateRecord
{
    protected static string $resource = OrcamentoResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
    public static $model = Orcamento::class;

    protected function beforeCreate(): void
    {
        $orcamentoId = $this->data['id_programa'] ?? null;
        // Define o modelo_type como o nome qualificado da classe do orçamento
        $modeloType = static::$model;

        // Obtém o ID da etapa inicial do workflow (suponhamos que seja 1)
        $etapaInicialId = 1;

        // Verificar se os IDs estão definidos
        if ($orcamentoId === null || $modeloType === null || $etapaInicialId === null) {
            // Se algum dos IDs não estiver definido, interrompa o processo de criação
            $this->halt();
            return;
        }

        // Verificar se já existe um registro com as mesmas condições
        $existingRecord = WorkflowItem::where('modelo_type', $modeloType)
            ->where('modelo_id', $orcamentoId)
            ->where('etapa_atual_id', $etapaInicialId)
            ->exists();

        // Se já existir um registro com as mesmas condições, interrompa o processo de criação
        if ($existingRecord) {
            // Emitir uma notificação de erro
            Notification::make()
                ->danger()
                ->duration(5000)
                ->title('Erro ao criar registro')
                ->body('Já existe um registro com os mesmos registos de programa, subprograma e beneficiario.')
                ->send();

            // Interromper o processo de criação
            $this->halt();
        } 
    }
    protected function afterCreate(): void
    {
        try {

            // Obtém o ID do novo orçamento criado
            $orcamentoId = $this->record->id;

            // Define o modelo_type como o nome qualificado da classe do orçamento
            $modeloType = static::$model;

            // Obtém o ID da etapa inicial do workflow (suponhamos que seja 1)
            $etapaInicialId = 1;

            // Cria um novo item de workflow associado ao orçamento
            WorkflowItem::create([
                'modelo_type' => $modeloType,
                'modelo_id' => $orcamentoId,
                'etapa_atual_id' => $etapaInicialId,
            ]);

            Notification::make()
                ->title('Orcamento Adicionado com sucesso')
                ->body('O seu orcamento foi adicionado com sucesso ')
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
