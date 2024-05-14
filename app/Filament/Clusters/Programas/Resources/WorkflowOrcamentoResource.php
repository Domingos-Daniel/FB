<?php

namespace App\Filament\Clusters\Programas\Resources;

use App\Filament\Clusters\Programas;
use App\Filament\Clusters\Programas\Resources\WorkflowOrcamentoResource\Pages;
use App\Filament\Clusters\Programas\Resources\WorkflowOrcamentoResource\RelationManagers;
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

class WorkflowOrcamentoResource extends Resource
{
    protected static ?string $model = WorkflowOrcamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('prox_passo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->label('Aprovar/Reprovar')
                    ->button()
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
                            'pendente' => 'Pendente',
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
                            ->badge()
                            ->label('Descrição do Orçamento')
                            ->color('info')
                            ->html()
                            ->copyable()
                            ->copyMessage('Copiado!')
                            ->copyMessageDuration(1500),
                        Components\TextEntry::make('orcamento.valor')
                            ->badge()
                            ->label('Valor do Orçamento')
                            ->money('USD', true)
                            ->color(function ($record) {
                                // Retorna a cor com base na condição ternária
                                return $record->orcamento->valor < 500000 ? 'danger' : 'success';
                            }),
                        Components\TextEntry::make('status')
                            ->badge()
                            ->label('Status')
                            ->icon(function (WorkflowOrcamento $record) {
                                if ($record->status === 'aprovado')
                                    return 'heroicon-o-check-circle';
                                elseif ($record->status === 'rejeitado')
                                    return 'heroicon-o-x-circle';
                                else
                                    return 'heroicon-o-exclamation-circle';
                            })
                            ->weight(FontWeight::Bold)
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
                            ->badge()
                            ->label('Motivo de Rejeição')
                            ->color('danger')
                            ->visible(fn ($record) => $record->status === 'rejeitado'),
                        Components\TextEntry::make('prox_passo')
                            ->badge()
                            ->label('Próximo Passo')
                            ->color('info'),
                        Components\TextEntry::make('num_aprovacoes_necessarias')
                            ->badge()
                            ->label('Número de Aprovações Necessárias')
                            ->color('info'),
                        Actions::make([
                            InfolistAction::make('status')
                                ->label('Aprovar Orçamento')
                                ->icon('heroicon-o-check-circle')
                                ->disabled(fn ($record) => $record->status === 'aprovado')
                                ->requiresConfirmation()
                                ->action(function (WorkflowOrcamento $record) {
                                    $record->status = "aprovado";
                                    $record->save();
                                }),
                            InfolistAction::make('updateStatus')
                                ->label('Reprovar Orçamento')
                                ->button()
                                ->icon('heroicon-o-x-circle')
                                ->modalHeading('Alterar Status')
                                ->disabled(fn ($record) => $record->status === 'rejeitado')
                                ->modalButton('Próximo')
                                ->color('danger')
                                ->steps([
                                    Step::make('Status')
                                        ->description('Selecione o status do orçamento')
                                        ->schema([
                                            Select::make('status')
                                                ->label('Status')
                                                ->options([
                                                    'rejeitado' => 'Rejeitado',
                                                    // Adicione outros estados conforme necessário
                                                ])
                                                ->required(),
                                        ]),
                                    Step::make('Motivo')
                                        ->description('Forneça um motivo de rejeição (se aplicável)')
                                        ->schema([
                                            MarkdownEditor::make('motivo_rejeicao')
                                                ->label('Motivo da Rejeição')
                                                ->required()
                                        ]),
                                    Step::make('Confirmação')
                                        ->description('Confirme a atualização')
                                        ->schema([
                                            TextInput::make('confirmation')
                                                ->label('Digite "CONFIRMAR" para confirmar esta ação')
                                                ->required()
                                                ->hint('Confirme a ação')
                                            //->helpMessage('Digite "confirmar" para confirmar a ação')
                                            ,
                                        ]),
                                ])

                                ->action(function (array $data, WorkflowOrcamento $record): void {
                                    // Validação do campo de confirmação
                                    if ($data['status'] === 'rejeitado' && strtolower($data['confirmation'] ?? '') !== 'confirmar') {
                                        throw ValidationException::withMessages(['confirmation' => 'Digite "confirmar" para confirmar a ação']);
                                    }
                                    $record->status = $data['status'];

                                    // Se o status selecionado for 'rejeitado', salva o motivo da rejeição
                                    if ($data['status'] === 'rejeitado') {
                                        $record->motivo_rejeicao = $data['motivo_rejeicao'] ?? null;
                                    }

                                    $record->save();
                                }),


                        ])->fullWidth(),


                    ])->grow(true),
                ])->from('md'),

                Components\Split::make([
                    Components\Section::make([
                        Components\TextEntry::make('created_at')
                            ->dateTime(format: "d/m/Y H:i:s")
                            ->label('Criado em'),
                        Components\TextEntry::make('updated_at')
                            ->dateTime(format: "d/m/Y H:i:s")
                            ->label('Atualizado em'),
                    ])->grow(true),
                ]),
            ]);
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
