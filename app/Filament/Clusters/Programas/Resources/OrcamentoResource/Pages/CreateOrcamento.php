<?php

namespace App\Filament\Clusters\Programas\Resources\OrcamentoResource\Pages;

use App\Filament\Clusters\Programas\Resources\OrcamentoResource;
use App\Models\Orcamento;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkflowOrcamento;
//use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Notifications\Notification as NotificationsNotification;

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
                Notification::make()
                    ->title('Valor do ID nao encontrado')
                    ->body('Por favor, tente novamente')
                    ->warning()
                    ->send();
            }

            // Obter o valor do orçamento associado ao programa
            $valorOrcamento = $this->record['valor'] ?? null;

            // Verifica se o valor do orçamento foi obtido
            if ($valorOrcamento === null) {
                throw new \Exception('Valor do orçamento não foi encontrado.');
                Notification::make()
                    ->title('Valor do Orcamento nao encontrado')
                    ->body('Por favor, tente novamente')
                    ->warning()
                    ->send();
            }

            // Cria o registro de Workflow de Orçamento
            WorkflowOrcamento::create([
                'orcamento_id' => $orcamentoId,
                'status' => 'pendente', // Status inicial como "pendente"
                'prox_passo' => 'Aprovação Diretor Geral', // Próximo passo do workflow
                'num_aprovacoes_necessarias' => ($valorOrcamento > 500000) ? 2 : 1, // Número de aprovações necessárias
            ]);


            try {
                // Obter o ID do novo orçamento criado
                $orcamentoId = $this->record['id'];

                // Encontrar o registro de WorkflowOrcamento associado ao novo orçamento
                $workflowOrcamento = WorkflowOrcamento::where('orcamento_id', $orcamentoId)->first();

                if (!$workflowOrcamento) {
                    throw new \Exception('Registro de WorkflowOrcamento não encontrado para o novo orçamento.');
                    Notification::make()
                        ->title('Erro')
                        ->body('Registro de WorkflowOrcamento não encontrado para o novo orçamento.')
                        ->warning()
                        ->send();
                }

               
                $rolesPorPasso = [
                    'Aprovação Diretor Geral' => 'DG',
                    'Aprovação CA curadores' => 'Admin',
                    // Adicione mais mapeamentos conforme necessário
                ];

                // Obter o próximo passo do workflow
                $proximoPasso = $workflowOrcamento->prox_passo;
                $idWorkflow = $workflowOrcamento->id;

                // Verificar se há uma role correspondente para o próximo passo
               
                // Notification::make()
                //     ->title('Passos')
                //     ->body($proximoPasso)
                //     ->warning()
                //     ->sendToDatabase(\auth()->user());

                // Verificar se há uma role correspondente para o próximo passo
                if (!isset($rolesPorPasso[$proximoPasso])) {
                    throw new \Exception('Nenhuma role encontrada para o próximo passo.');
                    Notification::make()
                        ->title('Erro')
                        ->body('Nenhuma role encontrada para o passo do workflow: ' . $rolesPorPasso[$proximoPasso])
                        ->warning()
                        ->sendToDatabase(\auth()->user());
                }

                // Encontrar os usuários que possuem essa role
                $usuariosProximoPasso = User::role($rolesPorPasso[$proximoPasso])->get();
                // Notification::make()
                //     ->title('Users')
                //     ->body($usuariosProximoPasso)
                //     ->success()
                //     ->sendToDatabase(\auth()->user());

                if ($usuariosProximoPasso->isEmpty()) {
                    throw new \Exception('Nenhum usuário encontrado para o próximo passo.');
                    Notification::make()
                        ->title('Erro')
                        ->body('Nenhum usuário encontrado para o próximo passo.' . $rolesPorPasso[$proximoPasso])
                        ->warning()
                        ->sendToDatabase(\auth()->user());
                }

                // Enviar uma notificação para cada usuário encontrado
                foreach ($usuariosProximoPasso as $usuario) {
                    Notification::make()
                        ->title('Novo Orçamento por Aprovar')
                        ->body('Há um novo orçamento a ser aprovado. Por favor, verifique!')
                        ->warning()
                        ->persistent()
                        ->actions([
                            Action::make('view')
                                ->button()
                                ->url(route('filament.admin.programas.resources.workflow-orcamentos.view', $idWorkflow)),
                        ])
                        ->sendToDatabase($usuario);
                    
                }
                
                Notification::make()
                        ->title('Notificacão Enviada')
                        ->body('Foi enviada uma notificação para ' . $proximoPasso.' com sucesso, o orcamento criado sera avaliado!')
                        ->info()
                        ->persistent()
                        ->send()
                        ->sendToDatabase(auth()->user());

            } catch (\Exception $e) {
                Notification::make()
                    ->title('Erro ao enviar notificação')
                    ->persistent()
                    ->body('Erro ao enviar notificação para o próximo passo do workflow: ' . $e->getMessage())
                    ->danger()
                    ->send();
            }

            if ($valorOrcamento < 500000) {
                # code...
                Notification::make()
                    ->title('Orcamento Pendente')
                    ->body('O seu orcamento passara para aprovação do diretor geral')
                    ->warning()
                    ->persistent()
                    ->sendToDatabase(\auth()->user())
                    ->send();
            } else {
                Notification::make()
                    ->title('Orcamento Pendente')
                    ->persistent()
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
