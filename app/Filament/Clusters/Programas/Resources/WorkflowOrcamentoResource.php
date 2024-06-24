<?php

namespace App\Filament\Clusters\Programas\Resources;

use App\Filament\Clusters\Programas;
use App\Filament\Clusters\Programas\Resources\WorkflowOrcamentoResource\Pages;
use App\Filament\Clusters\Programas\Resources\WorkflowOrcamentoResource\RelationManagers;
use App\Models\Aprovado;
use App\Models\WorkflowOrcamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Orcamento;
use App\Models\PassosWorkflow;
use App\Models\User;
use Filament\Actions\Modal\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\SelectColumn;
use Filament\Infolists\Components;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\ViewEntry;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Illuminate\Validation\ValidationException;
use Filament\Forms\Get;
use Closure;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Actions\Action as NotificationAction;
use PHPUnit\Framework\TestStatus\Notice;
use Illuminate\Support\Str;

class WorkflowOrcamentoResource extends Resource
{
    protected static ?string $model = WorkflowOrcamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Gestão Orçamental';
    protected static ?string $modelLabel = 'WorkFlow';
    protected static ?string $pluralModelLabel = 'WorkFlows';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $cluster = Programas::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('orcamento_id')
                    ->label('ID Orcamento')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->label('Status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('prox_passo')
                    ->label('Proximo Passo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('num_aprovacoes_necessarias')
                    ->required()
                    ->label('Numero de Aprovacoes Necessarias')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $orcamentos = Orcamento::pluck('valor', 'id')->toArray();
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('orcamento.descricao')
                    ->label('Descricao do Orcamento')
                    ->limit(30)
                    ->html()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('orcamento.valor')
                    ->label('Valor do Orcamento')
                    ->numeric()
                    ->badge()
                    ->color(function ($record) {
                        $valor = (int) $record->valor; // Acessa a propriedade 'valor' do objeto $record
                        // Retorna a cor com base na condição ternária
                        return $valor < 500000 ? 'danger' : 'success';
                    })
                    ->money('USD', true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->icon(function (WorkflowOrcamento $record) {
                        if ($record->status === 'aprovado')
                            return 'heroicon-o-check-circle';
                        elseif ($record->status === 'rejeitado')
                            return 'heroicon-o-x-circle';
                        else
                            return 'heroicon-o-exclamation-circle';
                    })
                    ->color(function (WorkflowOrcamento $record) {
                        if ($record->status === 'aprovado')
                            return 'success';
                        elseif ($record->status === 'rejeitado')
                            return 'danger';
                        else
                            return 'warning';
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('motivo_rejeicao')
                    ->label('Motivo Rejeição')
                    ->html()
                    ->limit(20)
                    ->getStateUsing(function ($record) {
                        return $record->status === 'rejeitado' ? $record->motivo_rejeicao : '--';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('prox_passo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('processadopor.name')
                    ->label('Processado Por')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-user')
                    ->sortable(),
                Tables\Columns\TextColumn::make('criador.name')
                    ->label('Criado Por')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-user')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('num_aprovacoes_necessarias')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('updateStatus')
                    ->label(fn ($record) => $record->status === 'pendente' ? 'Aprovar/Reprovar' : 'Orçamento Processado')
                    ->button()
                    ->hidden(fn ($record) => $record->status === 'aprovado' || $record->status === 'rejeitado')
                    ->disabled(fn ($record) => $record->status === 'aprovado' || $record->status === 'rejeitado')
                    ->icon('heroicon-o-pencil-square')
                    ->modalHeading('Alterar Status')
                    ->modalButton('Alterar Status')
                    ->modalSubheading('Alterar o status do registro')
                    ->color('info')
                    ->fillForm(function (WorkflowOrcamento $record): array {
                        return [
                            'status' => $record->status,
                        ];
                    })
                    ->form(function ($data) {
                        // Obtenha o status atual
                        $status = $data['status'] ?? null;

                        // Defina as opções disponíveis
                        $options = [
                            'aprovado' => 'Aprovado',
                            'rejeitado' => 'Rejeitado',
                            // Adicione outros estados conforme necessário
                        ];

                        // Crie o campo de select para o status
                        $selectStatus = Select::make('status')
                            ->label('Status')
                            ->options($options)
                            ->required();

                        // Se o status selecionado for 'rejeitado', adicione o campo para o motivo da rejeição
                        if ($status === 'rejeitado') {
                            $selectStatus->afterStateUpdated(function ($set, $state) {
                                $set('motivo_rejeicao', '');
                            });
                        }

                        return [
                            $selectStatus,
                            RichEditor::make('motivo_rejeicao')
                                ->label('Motivo da Rejeição')
                                ->default(function (WorkflowOrcamento $record): array {
                                    return [
                                        'status' => $record->status,
                                    ];
                                })
                                ->maxLength(255)
                                ->required(fn ($get) => $get('status') == 'rejeitado')
                                ->visible(fn ($get) => $get('status') == 'rejeitado')
                                ->hidden(fn ($get) => $get('status') !== 'rejeitado'),
                        ];
                    })
                    ->action(function (array $data, WorkflowOrcamento $record): void {
                        $record->status = $data['status'];

                        // Se o status selecionado for 'rejeitado', salva o motivo da rejeição
                        if ($data['status'] === 'rejeitado') {
                            $record->motivo_rejeicao = $data['motivo_rejeicao'] ?? null;
                        }

                        $record->save();

                        // // Find the PassosWorkflow record based on the prox_passo value
                        // $passoWorkflow = PassosWorkflow::where('nome', $record->prox_passo)->first();
                        // // Salva na tabela aprovados
                        // $aprovado = new Aprovado();
                        // $aprovado->workflow_orcamento_id = $record->id;
                        // $aprovado->passo_workflow_id = $passoWorkflow;// set the passo_workflow_id value here
                        // $aprovado->status = $data['status'];
                        // $aprovado->usuario_id = auth()->id(); // or get the user ID from the $data array if available
                        // $aprovado->save();
                    })

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Split::make([
                    Components\Section::make([
                        Components\TextEntry::make('orcamento.descricao')
                            ->label('Descrição do Orçamento')
                            ->color('info')
                            ->html()
                            ->copyable()
                            ->copyMessage('Copiado!')
                            ->copyMessageDuration(1500),
                        Components\TextEntry::make('orcamento.valor')
                            ->badge()
                            ->weight(FontWeight::Bold)
                            ->label('Valor do Orçamento')
                            ->money('USD', true)
                            ->color(function ($record) {
                                // Retorna a cor com base na condição ternária
                                return $record->orcamento->valor < 500000 ? 'danger' : 'success';
                            }),
                        Components\TextEntry::make('status')
                            ->badge()
                            ->weight(FontWeight::Bold)
                            ->label('Status')
                            ->html()
                            ->getStateUsing(function (WorkflowOrcamento $record) {
                                if ($record->num_aprovacoes_necessarias === 1) {
                                    return $record->status;
                                } else if ($record->num_aprovacoes_necessarias === 2) {
                                    if ($record->prox_passo === 'Aprovação CA Curadores') {
                                        return '1ª Etapa: ' . $record->status;
                                    } else if ($record->prox_passo === 'Finalizado') {
                                        return '1ª Etapa: ' . $record->status.'&nbsp; &nbsp; 2ª Etapa: ' . $record->status;
                                    } else {
                                        return '<b>Pendente</b> Nenhum do aprovadores processou';
                                    }
                                } else {
                                    return 'Desconhecido';
                                }
                            })
                            ->icon(function (WorkflowOrcamento $record) {
                                if ($record->status === 'aprovado')
                                    return 'heroicon-o-check-circle';
                                elseif ($record->status === 'rejeitado')
                                    return 'heroicon-o-x-circle';
                                else
                                    return 'heroicon-o-exclamation-circle';
                            })
                            ->weight(FontWeight::Black)
                            ->color(function ($record) {
                                // Retorna a cor com base no status
                                if ($record->status === 'aprovado') {
                                    return 'success';
                                } elseif ($record->status === 'rejeitado') {
                                    return 'danger';
                                } else {
                                    return 'warning';
                                }
                            }),
                        Components\TextEntry::make('motivo_rejeicao')
                            ->label('Motivo de Rejeição')
                            ->html()
                            ->visible(fn ($record) => $record->status === 'rejeitado')
                            ->color('danger')
                            ->visible(fn ($record) => $record->status === 'rejeitado'),
                        Components\TextEntry::make('prox_passo')
                            ->badge()
                            ->weight(FontWeight::Bold)
                            ->label('Próximo Passo')
                            ->color(function ($record) {
                                // Retorna a cor com base no proximo passo
                                if ($record->prox_passo === 'Aprovação Diretor Geral') {
                                    return 'info';
                                } else if ($record->prox_passo === 'Aprovação CA Curadores') {
                                    return 'success';
                                } else {
                                    return 'danger';
                                }
                            }),
                        Components\TextEntry::make('num_aprovacoes_necessarias')
                            ->badge()
                            ->weight(FontWeight::Bold)
                            ->label('Aprovações Necessárias')
                            ->getStateUsing(function (WorkflowOrcamento $record) {
                                if ($record->num_aprovacoes_necessarias === 1) {
                                    return 'Aprovação do Diretor Geral';
                                } else if ($record->num_aprovacoes_necessarias === 2) {
                                    return 'Aprovação do Diretor Geral E Aprovações dos CA Curadores';
                                } else {
                                    return 'Desconhecido';
                                }
                            })
                            ->color(function ($record) {
                                // Retorna a cor com base no status
                                if ($record->num_aprovacoes_necessarias === 1) {
                                    return 'info';
                                } elseif ($record->num_aprovacoes_necessarias === 2) {
                                    return 'success';
                                } else {
                                    return 'warning';
                                }
                            }),
                        Actions::make([
                            InfolistAction::make('status')
                                ->label(
                                    function ($record) {
                                        if ($record->status === 'pendente') {
                                            return 'Aprovar Orçamento';
                                        } else {

                                            if ($record->num_aprovacoes_necessarias === 1 && $record->prox_passo === 'Finalizado') {
                                                return 'Orçamento Processado';
                                            }
                                            // If two approvals are needed, check the user role
                                            else if ($record->num_aprovacoes_necessarias === 2 && $record->prox_passo === 'Finalizado') {
                                                return 'Orçamento Processado';
                                            }
                                        }
                                    }
                                )
                                ->icon('heroicon-o-check-circle')
                                ->disabled(function ($record) {
                                    $user = auth()->user();
                                    $userRole = $user->roles()->pluck('name')->first();
                                    $isDisabled = false;

                                    // If the record is in the final stage or rejected, disable it
                                    if ($record->prox_passo === 'Finalizado' || $record->status === 'rejeitado') {
                                        return true;
                                    }

                                    // Check if the status is 'pendente'
                                    if ($record->status === 'pendente') {
                                        $requiredRole = 'DG';

                                        // If only one approval is needed, check the user role
                                        if ($record->num_aprovacoes_necessarias === 1) {
                                            if ($userRole !== $requiredRole) {
                                                return true;
                                            }
                                        }
                                        // If two approvals are needed, check the user role
                                        else if ($record->num_aprovacoes_necessarias === 2) {
                                            if ($userRole !== $requiredRole) {
                                                return true;
                                            }
                                        }
                                        // If the number of approvals needed is not 1 or 2, disable it
                                        else {
                                            return true;
                                        }
                                    }
                                    // Check if the status is 'aprovado'
                                    else if ($record->status === 'aprovado') {
                                        $requiredRole = 'Admin';

                                        // If only one approval is needed, check the user role
                                        if ($record->num_aprovacoes_necessarias === 1) {
                                            if ($userRole !== $requiredRole) {
                                                return true;
                                            }
                                        }
                                        // If two approvals are needed, check the user role
                                        else if ($record->num_aprovacoes_necessarias === 2) {
                                            if ($userRole !== $requiredRole) {
                                                return true;
                                            }
                                        }
                                        // If the number of approvals needed is not 1 or 2, disable it
                                        else {
                                            return true;
                                        }
                                    }

                                    return $isDisabled;
                                })

                                ->requiresConfirmation()
                                ->action(function (WorkflowOrcamento $record) {
                                    $record->status = "aprovado";
                                    $record->processado_por = auth()->id();

                                    switch ($record->num_aprovacoes_necessarias) {
                                        case 1:
                                            $record->prox_passo = 'Finalizado';
                                            break;
                                        case 2:
                                            if ($record->prox_passo === 'Aprovação Diretor Geral') {

                                                $record->prox_passo = 'Aprovação CA Curadores';
                                            } else {
                                                $record->prox_passo = 'Finalizado';
                                            }

                                            break;
                                        default:
                                            $record->prox_passo = 'Finalizado';
                                            break;
                                    }


                                    $record->save();
                                    $id = $record->orcamento_id;
                                    $status = $record->status;
                                    $prox = $record->prox_passo;

                                    // // Find the PassosWorkflow record based on the prox_passo value
                                    // $passoWorkflow = PassosWorkflow::where('nome', $record->prox_passo)->first();
                                    // // Salva na tabela aprovados
                                    // $aprovado = new Aprovado();
                                    // $aprovado->workflow_orcamento_id = $record->id;
                                    // $aprovado->passo_workflow_id = $passoWorkflow;// set the passo_workflow_id value here
                                    // $aprovado->status = $status;
                                    // $aprovado->usuario_id = auth()->id(); // or get the user ID from the $data array if available
                                    // $aprovado->save();

                                    // Notificação de sucesso para o usuário atual
                                    Notification::make()
                                        ->title('Notificação Enviada')
                                        ->body('Foi enviada uma notificação para o usuário com sucesso.')
                                        ->info()
                                        ->persistent()
                                        ->sendToDatabase(auth()->user());
                                    WorkflowOrcamentoResource::sendNotifications($id, $status, $prox);
                                }),
                            InfolistAction::make('updateStatus')
                                ->label(fn ($record) => $record->status === 'pendente' ? 'Rejeitar Orçamento' : 'Orçamento Processado')
                                ->button()
                                ->icon('heroicon-o-x-circle')
                                ->modalHeading('Alterar Status')
                                ->disabled(function ($record) {
                                    $user = auth()->user();
                                    $userRole = $user->roles()->pluck('name')->first();
                                    $isDisabled = false;

                                    // If the record is in the final stage or rejected, disable it
                                    if ($record->prox_passo === 'Finalizado' || $record->status === 'rejeitado') {
                                        return true;
                                    }

                                    // Check if the status is 'pendente'
                                    if ($record->status === 'pendente') {
                                        $requiredRole = 'DG';

                                        // If only one approval is needed, check the user role
                                        if ($record->num_aprovacoes_necessarias === 1) {
                                            if ($userRole !== $requiredRole) {
                                                return true;
                                            }
                                        }
                                        // If two approvals are needed, check the user role
                                        else if ($record->num_aprovacoes_necessarias === 2) {
                                            if ($userRole !== $requiredRole) {
                                                return true;
                                            }
                                        }
                                        // If the number of approvals needed is not 1 or 2, disable it
                                        else {
                                            return true;
                                        }
                                    }
                                    // Check if the status is 'aprovado'
                                    else if ($record->status === 'aprovado') {
                                        $requiredRole = 'Admin';

                                        // If only one approval is needed, check the user role
                                        if ($record->num_aprovacoes_necessarias === 1) {
                                            if ($userRole !== $requiredRole) {
                                                return true;
                                            }
                                        }
                                        // If two approvals are needed, check the user role
                                        else if ($record->num_aprovacoes_necessarias === 2) {
                                            if ($userRole !== $requiredRole) {
                                                return true;
                                            }
                                        }
                                        // If the number of approvals needed is not 1 or 2, disable it
                                        else {
                                            return true;
                                        }
                                    }

                                    return $isDisabled;
                                })


                                ->modalButton('Próximo')
                                ->color('danger')
                                ->steps([
                                    Step::make('Status do Orçamento')
                                        ->description('O status do orçamento está selecionado como Rejeitado')
                                        ->schema([
                                            Select::make('status')
                                                ->label('Status do Orçamento, no momento está Pendente')
                                                ->options([
                                                    'rejeitado' => 'Rejeitado',
                                                    // Adicione outros estados conforme necessário
                                                ])
                                                ->default('rejeitado')
                                                ->disabled()
                                                ->required(),
                                            Hidden::make('processado_por')
                                                ->default(Auth::id()),
                                        ]),
                                    Step::make('Motivo de Rejeição')
                                        ->description('Forneça um motivo de rejeição (se aplicável)')
                                        ->schema([
                                            MarkdownEditor::make('motivo_rejeicao')
                                                ->label('Motivo da Rejeição')
                                                ->minLength(5)
                                                ->required(),
                                        ]),
                                    Step::make('Confirmação de Ação')
                                        ->description('Confirme a atualização')
                                        ->schema([
                                            TextInput::make('confirmation')
                                                ->label('Esta ação não pode ser desfeita. Se pretende revogar este orçamento, digite "CONFIRMAR" para confirmar esta ação')
                                                ->required()
                                                ->rules([
                                                    fn ($get) => function (string $attribute, $value, $fail) use ($get) {
                                                        if ($get('status') === 'rejeitado' && strtolower($value) !== 'confirmar') {
                                                            $fail('Digite "CONFIRMAR" para confirmar a ação');
                                                        }
                                                    }
                                                ]),
                                        ]),
                                ])
                                ->action(function (array $data, WorkflowOrcamento $record): void {
                                    // Validação do campo de confirmação
                                    if (strtolower($data['confirmation'] ?? '') !== 'CONFIRMAR') {
                                        throw ValidationException::withMessages(['confirmation' => 'Digite "CONFIRMAR" para confirmar a ação']);
                                    }
                                    // if ($record->status === 'rejeitado') {
                                    //     $record->prox_passo = 'Finalizado';
                                    // }
                                    // if ($record->num_aprovacoes_necessarias === 1) {
                                    //     $record->prox_passo = 'Finalizado';
                                    // } elseif ($record->num_aprovacoes_necessarias === 2) {
                                    //     if ($record->prox_passo === 'Aprovação Diretor Geral') {

                                    //         $record->prox_passo = 'Aprovação CA Curadores';
                                    //     } else {
                                    //         $record->prox_passo = 'Finalizado';
                                    //     }
                                    // }
                                    $id = $record->orcamento_id;
                                    $status = $record->status;
                                    $prox = $record->prox_passo;
                                    WorkflowOrcamentoResource::sendNotifications($id, $status, $prox);

                                    $record->status = 'rejeitado';
                                    $record->processado_por = $data['processado_por'];
                                    $record->motivo_rejeicao = $data['motivo_rejeicao'] ?? null;

                                    $record->save();

                                    // // Find the PassosWorkflow record based on the prox_passo value
                                    // $passoWorkflow = PassosWorkflow::where('nome', $record->prox_passo)->first();
                                    // // Salva na tabela aprovados
                                    // $aprovado = new Aprovado();
                                    // $aprovado->workflow_orcamento_id = $record->id;
                                    // $aprovado->passo_workflow_id = $passoWorkflow;// set the passo_workflow_id value here
                                    // $aprovado->status = $status;
                                    // $aprovado->usuario_id = auth()->id(); // or get the user ID from the $data array if available
                                    // $aprovado->save();

                                    // Notificação de sucesso para o usuário atual
                                    Notification::make()
                                        ->title('Notificação Enviada')
                                        ->body('Foi enviada uma notificação para o usuário com sucesso.')
                                        ->info()
                                        ->persistent()
                                        ->sendToDatabase(auth()->user());
                                }),


                        ])->fullWidth(),


                    ])->grow(true),
                ])->from('md'),

                Components\Split::make([
                    Components\Section::make([
                        Components\TextEntry::make('created_at')
                            ->dateTime(format: "d/m/Y H:i:s")
                            ->badge()
                            ->icon(fn ($record) => $record->status === 'pendente' ? 'heroicon-o-clock' : 'heroicon-o-check-circle')
                            ->color(fn ($record) => $record->status === 'pendente' ? 'info' : 'success')
                            ->label('Criado em'),
                        Components\TextEntry::make('updated_at')
                            ->dateTime(format: "d/m/Y H:i:s")
                            ->badge()
                            ->icon(fn ($record) => $record->status === 'pendente' ? 'heroicon-o-clock' : 'heroicon-o-check-circle')
                            ->color(fn ($record) => $record->status === 'pendente' ? 'info' : 'success')
                            ->label(fn ($record) => $record->status === 'pendente' ? 'Atualizado em: ' : 'Processado em: '),
                        Components\TextEntry::make('criador.name')
                            ->badge()
                            ->icon('heroicon-o-user')
                            ->color('info')
                            ->label('Criado Por'),
                        Components\TextEntry::make('processadopor.name')
                            ->badge()
                            ->icon('heroicon-o-user')
                            ->color('info')
                            ->label('Ultima operação feita por'),


                    ])->grow(true),
                ]),
            ]);
    }

    public static function sendNotifications($id, $status, $prox)
    {
        try {
            // Obter o registro de WorkflowOrcamento associado ao orçamento
            $workflowOrcamento = WorkflowOrcamento::where('orcamento_id', $id)->first();

            // Verificar se o registro foi encontrado
            if (!$workflowOrcamento) {
                throw new \Exception('Registro de WorkflowOrcamento não encontrado para o orçamento.');
            }

            // Mapear os passos do workflow para as roles correspondentes
            $rolesPorPasso = [
                'Aprovação Diretor Geral' => 'DG',
                'Aprovação CA Curadores' => 'Admin',
                // Adicione mais mapeamentos conforme necessário
            ];

            // Mapear os passos do workflow para os passos anteriores
            $passoAnterior = [
                'Finalizado' => 'Aprovação CA Curadores',
                'Aprovação CA Curadores' => 'Aprovação Diretor Geral',
                // Adicione mais mapeamentos conforme necessário
            ];

            // Obter o próximo passo do workflow
            $proximoPasso = $workflowOrcamento->prox_passo;
            $idWorkflow = $workflowOrcamento->id;

            // Obter o criador do WorkflowOrcamento
            $criador = User::find($workflowOrcamento->id_criador);

            // Verificar se o workflow está finalizado
            if ($prox === 'Finalizado') {
                // Enviar notificação para o criador
                if ($criador) {
                    Notification::make()
                        ->persistent()
                        ->title('Orçamento Finalizado')
                        ->body('O seu orçamento foi finalizado, por favor consulte o status!')
                        ->info()
                        ->actions([
                            NotificationAction::make('view')
                                ->label('Visualizar Orçamento')
                                ->color('info')
                                ->button()
                                ->url(route('filament.admin.programas.resources.workflow-orcamentos.view', $idWorkflow)),
                        ])
                        ->sendToDatabase($criador);
                }

                return 0;
            }

            // Enviar notificações se o orçamento foi reprovado
            if ($status === 'rejeitado') {
                // Notificação para o criador
                if ($criador) {
                    Notification::make()
                        ->persistent()
                        ->title('Orçamento Reprovado')
                        ->body('O seu orçamento foi reprovado, por favor veja os detalhes.')
                        ->danger()
                        ->actions([
                            NotificationAction::make('view')
                                ->label('Visualizar Orçamento')
                                ->color('danger')
                                ->button()
                                ->url(route('filament.admin.programas.resources.workflow-orcamentos.view', $idWorkflow)),
                        ])
                        ->sendToDatabase($criador);
                }

                // Verificar e notificar usuários da role do passo anterior
                if (isset($passoAnterior[$proximoPasso]) && isset($rolesPorPasso[$passoAnterior[$proximoPasso]])) {
                    $usuariosPassoAnterior = User::role($rolesPorPasso[$passoAnterior[$proximoPasso]])->get();
                    foreach ($usuariosPassoAnterior as $usuario) {
                        Notification::make()
                            ->persistent()
                            ->title('Orçamento Reprovado')
                            ->body('Um orçamento foi reprovado no passo: ' . $proximoPasso)
                            ->danger()
                            ->actions([
                                NotificationAction::make('view')
                                    ->label('Visualizar Orçamento')
                                    ->color('danger')
                                    ->button()
                                    ->url(route('filament.admin.programas.resources.workflow-orcamentos.view', $idWorkflow)),
                            ])
                            ->sendToDatabase($usuario);
                    }
                }
            }

            // Verificar se há uma role correspondente para o próximo passo
            if (!isset($rolesPorPasso[$proximoPasso])) {
                throw new \Exception('Nenhuma role encontrada para o próximo passo: ' . $proximoPasso);
            }

            // Encontrar os usuários que possuem a role correspondente ao próximo passo
            $usuariosProximoPasso = User::role($rolesPorPasso[$proximoPasso])->get();

            if ($usuariosProximoPasso->isEmpty()) {
                throw new \Exception('Nenhum usuário encontrado para o próximo passo: ' . $proximoPasso);
            }

            // Enviar notificações para cada usuário encontrado
            foreach ($usuariosProximoPasso as $usuario) {
                if ($status === 'aprovado') {
                    if ($proximoPasso === 'Aprovação CA Curadores') {
                        Notification::make()
                            ->persistent()
                            ->title('Novo Orçamento por Aprovar')
                            ->body('Há um novo orçamento a ser aprovado. Por favor, verifique!')
                            ->warning()
                            ->color('warning')
                            ->actions([
                                NotificationAction::make('view')
                                    ->label('Visualizar Orçamento')
                                    ->color('warning')
                                    ->button()
                                    ->url(route('filament.admin.programas.resources.workflow-orcamentos.view', $idWorkflow)),
                            ])
                            ->sendToDatabase($usuario);
                    }
                } elseif ($status === 'rejeitado') {
                    Notification::make()
                        ->persistent()
                        ->title('Orçamento reprovado')
                        ->body('O orçamento foi reprovado, por favor, veja os detalhes!')
                        ->danger()
                        ->actions([
                            NotificationAction::make('view')
                                ->label('Visualizar Orçamento')
                                ->color('danger')
                                ->button()
                                ->url(route('filament.admin.programas.resources.workflow-orcamentos.view', $idWorkflow)),
                        ])
                        ->sendToDatabase($usuario);
                }
            }
        } catch (\Exception $e) {
            // Notificação de erro
            Notification::make()
                ->title('Erro ao enviar notificação')
                ->body('Erro ao enviar notificação para o próximo passo do workflow: ' . $e->getMessage())
                ->danger()
                ->actions([
                    NotificationAction::make('view')
                        ->label('Visualizar Orçamento')
                        ->color('warning')
                        ->button()
                        ->url(route('filament.admin.programas.resources.workflow-orcamentos.view', $idWorkflow)),
                ])
                ->persistent()
                ->send();
        }
    }



    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkflowOrcamentos::route('/'),
            'create' => Pages\CreateWorkflowOrcamento::route('/create'),
            'view' => Pages\ViewWorkflowOrcamento::route('/{record}'),
            'edit' => Pages\EditWorkflowOrcamento::route('/{record}/edit'),
        ];
    }
}
