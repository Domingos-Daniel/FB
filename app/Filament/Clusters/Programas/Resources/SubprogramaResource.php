<?php

namespace App\Filament\Clusters\Programas\Resources;

use App\Filament\Clusters\Programas;
use App\Filament\Clusters\Programas\Resources\SubprogramaResource\Pages;
use App\Models\gasto;
use App\Models\Programa;
use App\Models\Subprograma;
use App\Models\Orcamento;
use App\Models\OrcamentoPrograma;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Closure;
use Filament\Infolists\Components;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Infolist;

class SubprogramaResource extends Resource
{
    protected static ?string $model = Subprograma::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $cluster = Programas::class;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        $programas = Programa::pluck('nome', 'id')->toArray();
        $orcamentos = Orcamento::pluck('valor', 'id')->toArray();
        $record = Subprograma::find(1);

        return $form
            ->schema([
                Forms\Components\Select::make('id_programa')
                    ->options($programas)
                    ->searchable()
                    ->live()
                    ->native(false) 
                    ->label("Selecione o Programa Social")
                    ->preload()
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Select::make('orcamento_id')
                    ->label('Valor atual disponível para este programa')
                    ->suffixIcon('heroicon-m-banknotes')
                    ->suffixIconColor('success')
                    ->hiddenOn('edit')
                    ->options(function (Get $get) {
                        $id_programa = $get('id_programa');

                        // Buscar o id_orcamento na tabela orcamentoprograma
                        $id_orcamento = OrcamentoPrograma::where('id_programa', $id_programa)->pluck('id_orcamento')->first();

                        // Buscar o valor do orçamento na tabela orcamento usando o id_orcamento
                        $valor_orcamento = optional(Orcamento::find($id_orcamento))->valor ?? 0;

                        // Acessar o valor total gasto para este orçamento
                        $valorGastoPrograma = Gasto::where('id_programa', $id_programa)->sum('valor_gasto');

                        // Calculando a diferença entre o valor total do orçamento e o valor gasto
                        $valor_disponivel = $valor_orcamento - $valorGastoPrograma;

                        // Retornar o valor disponível para o orçamento
                        return [$id_programa => $valor_disponivel];
                    })
                    ->default(function (Get $get) {
                        $id_programa = $get('id_programa');

                        // Buscar o id_orcamento na tabela orcamentoprograma
                        $id_orcamento = OrcamentoPrograma::where('id_programa', $id_programa)->pluck('id_orcamento')->first();

                        // Buscar o valor do orçamento na tabela orcamento usando o id_orcamento
                        $valor_orcamento = optional(Orcamento::find($id_orcamento))->valor ?? 0;

                        // Retornar o valor do orçamento como a opção padrão
                        return $valor_orcamento;
                    })
                    ->disabled()
                    ->selectablePlaceholder(false),
                Forms\Components\TextInput::make('designacao')
                    ->label("Designação")
                    ->unique(ignoreRecord: true)
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255),
                Forms\Components\TextInput::make('valor')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->numeric()
                    //->money('USD', divideBy: 100)
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            // Obtém o valor inserido no campo 'valor'
                            $valorInserido = (float) $value;

                            // Obtém o ID do programa selecionado
                            $idPrograma = (int) $get('id_programa');

                            // Buscar o id_orcamento na tabela orcamentoprograma
                            $id_orcamento = OrcamentoPrograma::where('id_programa', $idPrograma)->pluck('id_orcamento')->first();

                            // Buscar o valor do orçamento na tabela orcamento usando o id_orcamento
                            $orcamentoProgramaValor = optional(Orcamento::find($id_orcamento))->valor ?? null;

                            // Se não houver registro correspondente na tabela de orçamento, atribuir null a $orcamentoProgramaValor
                            if ($orcamentoProgramaValor === null) {
                                $orcamentoProgramaValor = null;
                            }

                            // Depura o valor de $orcamentoProgramaValor
                            //dd($orcamentoProgramaValor, ' - t');

                            // Se o valor do orçamento não estiver disponível, retornar uma mensagem indicando a falta de orçamento
                            if ($orcamentoProgramaValor === null) {
                                return '- Sem Orçamento Disponível';
                            }

                            // Acessar o valor total gasto para este orçamento
                            $valorGastoPrograma = Gasto::where('id_programa', $idPrograma)->sum('valor_gasto');

                            // Calculando o valor disponível para o orçamento
                            $orcamentoDisponivel = $orcamentoProgramaValor - $valorGastoPrograma;

                            // Verifica se o valor inserido é maior que o orçamento disponível
                            if ($valorInserido > $orcamentoDisponivel) {
                                Notification::make()
                                    ->danger()
                                    ->title('Erro no Formulário')
                                    ->body('O valor do subprograma não pode ser maior que o valor disponível do orçamento.')
                                    ->send();
                                $fail("O valor inserido para o subprograma é maior que o orçamento disponível para o programa.");
                            } elseif ($valorInserido == $orcamentoDisponivel) {
                                Notification::make()
                                    ->danger()
                                    ->title('Erro no Formulário')
                                    ->body('O valor do subprograma não pode ser igual ao valor disponível do orçamento.')
                                    ->send();
                                $fail("O valor inserido para o subprograma é igual ao valor do orçamento disponível para o programa, e poderá deixar zerada a caixa.");
                            }
                        },
                    ]),
                Forms\Components\Hidden::make('id_criador')
                    ->default(auth()->id()),
            ]);
    }

    public function orcamentoPrograma()
    {
        return $this->belongsTo(OrcamentoPrograma::class, 'id_programa', 'id_programa'); // Assuming foreign key is id_programa
    }


    // No seu recurso Laravel Nova, você pode adicionar um método estático para calcular a diferença
    public static function calcularDiferenca($record)
    {
        // Acessar o id do orçamento do programa
        $id_orcamento = optional(optional($record->orcamentoPrograma)->orcamento)->id;

        // Buscar o valor do orçamento na tabela orcamento usando o id_orcamento
        $orcamentoProgramaValor = optional(Orcamento::find($id_orcamento))->valor ?? 0;

        // Acessar o ID do programa
        $idPrograma = optional($record->orcamentoPrograma)->id_programa;

        // Acessar o total gasto para este orçamento
        $valorGastoPrograma = Gasto::where('id_programa', $idPrograma)->sum('valor_gasto');

        // Calculando a diferença entre o valor total do orçamento e o valor gasto
        $diferenca = $orcamentoProgramaValor - $valorGastoPrograma;

        return $diferenca;
    }





    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_programa')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('designacao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('programa.nome')
                    ->label('Programa Associado')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor')
                    ->label('Valor do Subprograma')
                    ->color('info')
                    ->money('USD', true)
                    ->numeric()
                    ->icon('heroicon-m-banknotes')
                    ->sortable(),
                Tables\Columns\TextColumn::make('orcamento_programa_valor_original')
                    ->label('Orçamento Original do Programa')
                    ->numeric()
                    ->money('USD', true)
                    ->sortable()
                    ->icon('heroicon-m-banknotes')
                    ->color(function ($record) {
                        // Usando o método estático para calcular a diferença
                        $diferenca = self::calcularDiferenca($record);

                        // Retorna a cor com base na condição ternária
                        return $diferenca < 1000000 ? 'danger' : 'success';
                    })
                    ->getStateUsing(function ($record) {

                        // Acessar o valor original do orçamento do programa a partir da relação definida no modelo Subprograma
                        $orcamentoPrograma = $record->orcamentoPrograma;
                        $valorOrcamento = '-';

                        if ($orcamentoPrograma) {
                            $orcamento = $orcamentoPrograma->orcamento;
                            if ($orcamento) {
                                $valorOrcamento = $orcamento->valor;
                            }
                        }

                        return $valorOrcamento ?? '- Sem Orçamento';
                    }),

                Tables\Columns\TextColumn::make('orcamento_programa_valor')
                    ->label('Orçamento Restante')
                    ->numeric()
                    ->money('USD', true)
                    ->sortable()
                    ->icon('heroicon-m-banknotes')
                    ->color(function ($record) {
                        // Usando o método estático para calcular a diferença
                        $diferenca = self::calcularDiferenca($record);

                        // Retorna a cor com base na condição ternária
                        return $diferenca < 1000000 ? 'danger' : 'success';
                    })
                    ->getStateUsing(function ($record) {
                        // Usando o método estático para calcular a diferença
                        return self::calcularDiferenca($record);
                    }),
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
                        Components\TextEntry::make('designacao')
                            ->badge()
                            ->label('Designação')
                            ->color('success'),
                        Components\TextEntry::make('programa.nome')
                            ->label('Programa Social')
                            ->badge()
                            ->weight(FontWeight::Bold)
                            ->color('info')
                            ->copyable()
                            ->copyMessage('Copiado!')
                            ->copyMessageDuration(1500),
                        Components\TextEntry::make('orcamento_programa_valor')
                            ->badge()
                            ->money('USD', true)
                            ->label('Orçamento do Programa')
                            ->icon('heroicon-m-banknotes')
                            ->color(function ($record) {
                                // Usando o método estático para calcular a diferença
                                $diferenca = self::calcularDiferenca($record);

                                // Retorna a cor com base na condição ternária
                                return $diferenca < 1000000 ? 'danger' : 'success';
                            })
                            ->getStateUsing(function ($record) {
                                // Acessar o valor original do orçamento do programa a partir da relação definida no modelo Subprograma
                                return optional($record->orcamentoPrograma->orcamento)->valor ?? '-';
                            }),

                        Components\TextEntry::make('valor')
                            ->label('Valor do SubPrograma')
                            ->badge()
                            ->numeric()
                            ->money('USD', true)
                            ->color(function ($record) {
                                // Usando o método estático para calcular a diferença
                                $diferenca = self::calcularDiferenca($record);

                                // Retorna a cor com base na condição ternária
                                return $diferenca < 1000000 ? 'danger' : 'success';
                            }),
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
            'index' => Pages\ListSubprogramas::route('/'),
            'create' => Pages\CreateSubprograma::route('/create'),
            'view' => Pages\ViewSubprograma::route('/{record}'),
            'edit' => Pages\EditSubprograma::route('/{record}/edit'),
        ];
    }
}
