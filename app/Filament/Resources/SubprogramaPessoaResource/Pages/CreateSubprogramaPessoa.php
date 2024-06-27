<?php

namespace App\Filament\Resources\SubprogramaPessoaResource\Pages;

use App\Filament\Resources\SubprogramaPessoaResource;
use App\Models\gasto;
use App\Models\OrcamentoPrograma;
use App\Models\Programa;
use App\Models\Subprograma;
use App\Models\SubprogramaPessoa;
use Dotenv\Exception\ValidationException;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class CreateSubprogramaPessoa extends CreateRecord
{
    protected static string $resource = SubprogramaPessoaResource::class;

    protected function beforeCreate(): void
    {
        $id_programa = $this->data['id_programa'] ?? null;
        $id_subprograma = $this->data['id_subprograma'] ?? null;
        $id_pessoa = $this->data['id_pessoa'] ?? null;

        // Verificar se os IDs estão definidos
        if ($id_programa === null || $id_subprograma === null || $id_pessoa === null) {
            // Se algum dos IDs não estiver definido, interrompa o processo de criação
            $this->halt();
            return;
        }

        // Verificar se já existe um registro com as mesmas condições
        $existingRecord = SubprogramaPessoa::where('id_programa', $id_programa)
            ->where('id_subprograma', $id_subprograma)
            ->where('id_pessoa', $id_pessoa)
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
            $subprogramapessoa = SubprogramaPessoa::findOrFail($this->record['id']);
            $id_programa = $this->record['id_programa'];

            // Obtém o ID do orçamento associado ao programa
            $orcamentoPrograma = OrcamentoPrograma::where('id_programa', $id_programa)->first();
            $id_orcamento = $orcamentoPrograma ? $orcamentoPrograma->id_orcamento : null;

            // Encontra o subprograma com base no id fornecido
            $subprograma = Subprograma::where('id', $this->record['id_subprograma'])->first();

            // Verifica se o subprograma foi encontrado
            if ($subprograma) {
                // Pega o valor da coluna 'valor' do subprograma
                $valor_gasto = (int) $subprograma->valor;
                $id_subprograma = $subprograma->id; // Pega o ID do subprograma
            } else {
                // Caso o subprograma não seja encontrado, define o valor gasto e id_subprograma como null ou algum valor padrão
                $valor_gasto = null;
                $id_subprograma = null;
            }

            // Cria o registro de Gasto
            $gasto = new Gasto();
            $gasto->id_programa = $id_programa;
            $gasto->id_subprograma = $id_subprograma; // Aqui deve ser o ID do subprograma, não o objeto
            $gasto->id_orcamento = $id_orcamento; // Utiliza o ID do orçamento obtido
            $gasto->valor_gasto = $valor_gasto;
            $gasto->id_subprograma_pessoas = $this->record['id']; // Atribui o ID do SubprogramaPessoa
            $gasto->save();

            Notification::make()
                ->title('Patrocinio Adicionado com sucesso')
                ->body('O seu patrocinio foi adicionado com sucesso ')
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
