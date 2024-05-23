<?php

namespace App\Filament\Clusters\Programas\Resources;

use App\Filament\Clusters\Programas;
use App\Filament\Clusters\Programas\Resources\OrcamentoResource\Pages;
use App\Filament\Clusters\Programas\Resources\OrcamentoResource\RelationManagers;
use App\Filament\Clusters\Programas\Resources\OrcamentoResource\Widgets\OrcamentoOverview;
use App\Models\Orcamento;
use App\Models\OrcamentoGeral;
use App\Models\Programa;
use App\Models\WorkflowOrcamento;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;

class OrcamentoResource extends Resource
{
    protected static ?string $model = Orcamento::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $modelLabel = 'Orçamento';
    protected static ?string $pluralModelLabel = 'Orçamentos';
    protected static ?string $navigationGroup = 'Gestão Orçamental';
    protected static ?string $cluster = Programas::class;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count(); 
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()->whereHas('workflowItem', function (Builder $query) {
    //         $query->where('etapa_atual_id', 2);
    //     });
    // }

    // public function obterRegistrosAprovados()
    // {
    //     // Consulta para recuperar registros aprovados no workflow
    //     $registrosAprovados = Orcamento::where('etapa_atual_id', 3)->get();
    
    //     // Faça algo com os registros aprovados...
    // }

    public static function form(Form $form): Form
    {
        $programas = Programa::pluck('nome', 'id')->toArray();
        $valor_orcamento_geral = OrcamentoGeral::whereYear('created_at', '>=', now()->format('Y'))
                                                ->pluck('valor_total')
                                                ->first();
        $isDisabled = false;
        if($valor_orcamento_geral == null){
            $isDisabled = true;
        }

        return $form
            ->schema([ 
                 // Forms\Components\TextInput::make('id_programa')
                //     ->required()
                //     ->numeric(),
                Forms\Components\TextInput::make('Valor Actual Orçamento Geral')
                    ->default(function () {
                        $valorGeral = OrcamentoGeral::whereYear('created_at', '>=', now()->format('Y'))
                            ->pluck('valor_total')
                            ->first();
                        $valorOrcamento = Orcamento::whereYear('created_at', now()->format('Y'))
                            ->sum('valor');

                        $orcamento_atual = $valorGeral - $valorOrcamento;
                        $formated = number_format($orcamento_atual, 2, ',', '.');
                        return $formated;
                    })
                    ->prefixIcon('heroicon-o-currency-dollar')
                    ->prefixIconColor('success')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->disabled(),
                Forms\Components\TextInput::make('valor')
                    ->label("Valor do Orçamento")
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->disabled($isDisabled)
                    ->numeric()
                    ->prefixIcon('heroicon-o-currency-dollar')
                    ->prefixIconColor('success')
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            // Obtém o valor inserido no campo 'valor'
                            $valorInserido = (float) $value;
                            // Buscar o id_orcamento na tabela orcamentoprograma
                            $valor_orcamento_geral = OrcamentoGeral::whereYear('created_at', '>=', now()->format('Y'))
                                                ->pluck('valor_total')
                                                ->first();

                            // Verifica se o valor inserido é maior que o valor do orcamento geral
                            if ($valorInserido >= $valor_orcamento_geral) {
                                Notification::make()
                                    ->danger()
                                    ->title('Erro no Formulário')
                                    ->body('O valor do orçamento não pode ser maior, nem igual ao valor do orçamento geral.')
                                    ->send();
                                $fail("O valor inserido para o orçamento é maior, ou igual ao valor do orçamento geral.");
                            }else if($valorInserido < 0){
                                Notification::make()
                                    ->danger()
                                    ->title('Erro no Formulário')
                                    ->body('O valor do orçamento não pode ser negativo.')
                                    ->send();
                                $fail("O valor inserido para o orçamento é negativo.");
                            }else if($valorInserido == 0){
                                Notification::make()
                                    ->danger()
                                    ->title('Erro no Formulário')
                                    ->body('O valor do orçamento não pode ser zero.')
                                    ->send();
                                $fail("O valor inserido para o orçamento é zero.");
                            } 
                        },
                    ]),
                Forms\Components\RichEditor::make('descricao')
                    ->required()
                    ->disabled($isDisabled)
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull()
                    ->minLength(5)
                    ->maxLength(1024) 
                    ->disabled($isDisabled)
                    ->label('Descrição do Orçamento'),
                Forms\Components\Hidden::make('id_criador')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        //$registrosAprovados = WorkflowItem::where('etapa_atual_id', 3)->get();
        return $table
        
            ->columns([
                Tables\Columns\TextColumn::make('descricao')
                    ->searchable()
                    ->label('Descricao do Orcamento')
                    ->limit(30)
                    ->badge()
                    ->html()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('valor')
                    ->numeric()
                    ->label('Valor do Orçamento')
                    ->money('USD', true)
                    ->color('info')
                    ->sortable(), 
                    Tables\Columns\TextColumn::make('workflow.status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->weight(FontWeight::Bold)
                    ->color(function ($record) {
                        // Retorna a cor com base na condição ternária
                        switch ($record->workflow->status) {
                            case 'aprovado':
                                return 'success';
                            case 'rejeitado':
                                return 'danger';
                            default:
                                return 'warning';
                        }
                    })
                    ->icon(function ($record) {
                        // Retorna o icone com base na condição ternária
                        switch ($record->workflow->status) {
                            case 'aprovado':
                                return 'heroicon-o-check-circle';
                            case 'rejeitado':
                                return 'heroicon-o-x-circle';
                            default:
                                return 'heroicon-o-exclamation-circle';
                        }
                    })
                    ->searchable(),                
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
               
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

    public static function getWidgets(): array
    {
        return [
            OrcamentoOverview::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrcamentos::route('/'),
            'create' => Pages\CreateOrcamento::route('/create'),
            'view' => Pages\ViewOrcamento::route('/{record}'),
            'edit' => Pages\EditOrcamento::route('/{record}/edit'),
        ];
    }

   
}
