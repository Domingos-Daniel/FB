<?php

namespace App\Observers;

use App\Models\Orcamento;
use App\Models\User;
use App\Models\WorkflowOrcamento;
use Filament\Notifications\Notification;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class OrcamentoObserver 
{
    /** 
     * Handle the Orcamento "created" event.
     */
    public function created(Orcamento $orcamento)
    {
        try {
            // Obter o ID do novo orçamento criado
            $orcamentoId = $orcamento->id;

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

            // Mapear os passos do workflow para as roles correspondentes
            $rolesPorPasso = [
                'Aprovação Diretor Geral' => 'DG',
                'Aprovação PCA' => 'PCA',
                // Adicione mais mapeamentos conforme necessário
            ];

            // Obter o próximo passo do workflow
            $proximoPasso = $workflowOrcamento->prox_passo;

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
                    ->sendToDatabase($usuario);
            }
            
            Notification::make()
                ->title('Notificação Enviada')
                ->body('Foi enviada uma notificação para ' . $proximoPasso . ' com sucesso. O orçamento criado será avaliado!')
                ->info()
                ->persistent()
                ->send()
                ->sendToDatabase(auth()->user());
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erro ao enviar notificação')
                ->body('Erro ao enviar notificação para o próximo passo do workflow: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    // Função para determinar a role correspondente ao passo do workflow
    private function determinarRolePorPasso($proximoPasso)
    {
        // Lógica para mapear o próximo passo para a role correspondente
        // Por exemplo, se o passo for "Aprovação Diretor Geral", a role é "DG"
        // Você pode implementar sua própria lógica de mapeamento aqui
        // Este é apenas um exemplo simples
        switch ($proximoPasso) {
            case 'Aprovação Diretor Geral':
                return 'DG';
            case 'Aprovação PCA':
                return 'PCA';
            // Adicione mais casos conforme necessário para outros passos
            default:
                return ''; // Retorna vazio se nenhum mapeamento for encontrado
        }
    }
    // protected function determinarProximaPessoa(Orcamento $orcamento): ?User
    // {
    //     // Obtém o workflow atual do orçamento
    //     $workflow = $orcamento->workflow;

    //     // Verifica se o workflow existe e se tem um próximo passo definido
    //     if ($workflow && $workflow->prox_passo) {
    //         // Obtém a próxima função (role) do passo seguinte do workflow
    //         $proximaFuncao = $workflow->prox_passo;

    //         // Consulta os usuários que têm a função (role) necessária
    //         $usuariosComFuncao = User::whereHas('roles', function ($query) use ($proximaFuncao) {
    //             $query->where('name', $proximaFuncao);
    //         })->get();

    //         // Retorna o primeiro usuário encontrado com a função (role) necessária
    //         return $usuariosComFuncao->first();
    //     }

    //     // Se não houver próximo passo no workflow, retorna null
    //     return null;
    // }

    /**
     * Handle the Orcamento "updated" event.
     */
    public function updated(Orcamento $orcamento): void
    {
        //
    }

    /**
     * Handle the Orcamento "deleted" event.
     */
    public function deleted(Orcamento $orcamento): void
    {
        //
    }

    /**
     * Handle the Orcamento "restored" event.
     */
    public function restored(Orcamento $orcamento): void
    {
        //
    }

    /**
     * Handle the Orcamento "force deleted" event.
     */
    public function forceDeleted(Orcamento $orcamento): void
    {
        //
    }
}
