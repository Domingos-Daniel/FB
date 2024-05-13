<?php

namespace App\Filament\Clusters\Programas\Resources\OrcamentoResource\Pages;

use App\Filament\Clusters\Programas\Resources\OrcamentoResource;
use App\Models\Orcamento;
use App\Models\WorkflowOrcamento;
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

    // protected function beforeCreate(): void
    // {
    //     // Verificar se os campos necessários estão presentes nos dados do formulário
    //     if (!isset($this->data['orcamento_id']) || !isset($this->data['status']) || !isset($this->data['prox_passo']) || !isset($this->data['num_aprovacoes_necessarias'])) {
    //         // Se algum dos campos estiver ausente, interrompa o processo de criação
    //         $this->halt();
    //         return;
    //     }
 
    //     // Obter o ID do orçamento
    //     $orcamentoId = $this->data['orcamento_id'];

    //     // Verificar se o orçamento já possui um registro de workflow
    //     $existingRecord = WorkflowOrcamento::where('orcamento_id', $orcamentoId)->exists();

    //     // Se já existir um registro de workflow para este orçamento, interrompa o processo de criação
    //     if ($existingRecord) {
    //         // Emitir uma notificação de erro
    //         Notification::make()
    //             ->danger()
    //             ->duration(5000)
    //             ->title('Erro ao criar registro')
    //             ->body('Já existe um registro de workflow para este orçamento.')
    //             ->send();

    //         // Interromper o processo de criação
    //         $this->halt();
    //         return;
    //     }

    //     // Se não houver nenhum problema, continue com o processo de criação
    // }
    protected function afterCreate(): void
    {
        try { 
            // Obtém o ID do orçamento associado ao programa
            $orcamentoId = $this->record['id'] ?? null;

            // Verifica se o ID do orçamento foi obtido
            if ($orcamentoId === null) {
                throw new \Exception('ID do orçamento não foi fornecido.');
                dd("ID do orçamento não foi fornecido.");
            }

            // Obter o valor do orçamento associado ao programa
            $valorOrcamento = $this->record['valor'] ?? null;

            // Verifica se o valor do orçamento foi obtido
            if ($valorOrcamento === null) {
                throw new \Exception('Valor do orçamento não foi encontrado.');
                dd("Valor do orçamento não foi encontrado.");
            }

            // Cria o registro de Workflow de Orçamento
            WorkflowOrcamento::create([
                'orcamento_id' => $orcamentoId,
                'status' => 'pendente', // Status inicial como "pendente"
                'prox_passo' => 'Aprovação Diretor Geral', // Próximo passo do workflow
                'num_aprovacoes_necessarias' => ($valorOrcamento > 500000) ? 2 : 1, // Número de aprovações necessárias
            ]);

                if ($valorOrcamento > 500000) {
                    # code...
                    Notification::make()
                    ->title('Orcamento Pendente')
                    ->body('O seu orcamento passara para aprovação do diretor geral')
                    ->warning()
                    ->sendToDatabase(\auth()->user())
                    ->send();
                } else {
                    Notification::make()
                    ->title('Orcamento Pendente')
                    ->body('O seu orcamento passara para aprovação diretor geral e por CA CURADORES')
                    ->warning()
                    ->sendToDatabase(\auth()->user())
                    ->send();
                }
                
            
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
