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
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->icon(function (WorkflowOrcamento $record) {
                        if($record->status === 'aprovado')
                            return 'heroicon-o-check-circle';
                        elseif($record->status === 'rejeitado')
                            return 'heroicon-o-x-circle';
                        else
                            return 'heroicon-o-exclamation-circle';
                    })
                    ->color(function (WorkflowOrcamento $record) {
                        if($record->status === 'aprovado')
                            return 'success';
                        elseif($record->status === 'rejeitado')
                            return 'danger';
                        else
                            return 'warning';
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('motivo_rejeicao')
                    ->label('Motivo Rejeição')
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
                            ->maxLength(255)
                            ->required()
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
