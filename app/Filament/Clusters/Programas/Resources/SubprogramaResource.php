<?php

namespace App\Filament\Clusters\Programas\Resources;

use App\Filament\Clusters\Programas;
use App\Filament\Clusters\Programas\Resources\SubprogramaResource\Pages;
use App\Filament\Clusters\Programas\Resources\SubprogramaResource\RelationManagers;
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

class SubprogramaResource extends Resource
{
    protected static ?string $model = Subprograma::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $cluster = Programas::class;

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
        
        // Access the budget value for the program
        $orcamentoProgramaValor = optional(Orcamento::find($id_programa))->valor ?? 0;
        
        // Access the total amount spent for this program's budget
        $valorGastoPrograma = Gasto::where('id_programa', $id_programa)->sum('valor_gasto');
        
        // Calculating the difference between the total budget value and the spent amount
        $valor_disponivel = $orcamentoProgramaValor - $valorGastoPrograma;
        
        // Return the available value for the budget
        return [$id_programa => $valor_disponivel];
    })
    ->default(function (Get $get) {
        $id_programa = $get('id_programa');
        
        // Carregar o valor do orçamento com base no programa social selecionado
        $valor_orcamento = Orcamento::where('id', $id_programa)->pluck('valor')->first();
        
        // Retornar o valor do orçamento como a opção padrão
        return $valor_orcamento;
    })
    ->disabled()
    ->selectablePlaceholder(false),

                
                

                Forms\Components\TextInput::make('designacao')
                    ->label("Designação")
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('valor')
                    ->required()
                    ->numeric()
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            // Obtém o valor inserido no campo 'valor'
                            $valorInserido = (float) $value;
                
                            // Obtém o orçamento disponível
                            $orcamentoDisponivel = $get('orcamento_disponivel');
                
                            // Verifica se o valor inserido é maior que o orçamento disponível
                            if ($valorInserido > $orcamentoDisponivel) {
                                Notification::make()
                                    ->danger()
                                    ->title('Erro no Formulario')
                                    ->body('O valor do subprograma não pode ser maior, nem igual ao valor disponível do orçamento.')
                                    ->send();
                                $fail("O valor inserido para o subprograma é maior que o orçamento disponível para o programa.");
                            }else if ($valorInserido == $orcamentoDisponivel){
                                $fail("O valor inserido para o subprograma é igual ao valor do orçamento disponível para o programa, e podera deixar zerada a caixa.");
                            }
                        },
                    ]),
            ]);
    }

    public function orcamentoPrograma()
    {
        return $this->belongsTo(OrcamentoPrograma::class, 'id_programa', 'id_programa'); // Assuming foreign key is id_programa
    }


    // No seu recurso Laravel Nova, você pode adicionar um método estático para calcular a diferença
    public static function calcularDiferenca($record)
    {
        // Access the budget value for the program
        $orcamentoProgramaValor = optional($record->orcamentoPrograma->orcamento)->valor ?? 0;

        // Access the total amount spent for this program's budget
        $idPrograma = $record->orcamentoPrograma->id_programa;
        $valorGastoPrograma = Gasto::where('id_programa', $idPrograma)->sum('valor_gasto');

        // Calculating the difference between the total budget value and the spent amount
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
                    ->numeric()
                    ->icon('heroicon-m-banknotes')
                    ->sortable(),
                Tables\Columns\TextColumn::make('orcamento_programa_valor_original')
                    ->label('Orçamento Original do Programa')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-m-banknotes')
                    ->getStateUsing(function ($record) {
                        // Acessar o valor original do orçamento do programa a partir da relação definida no modelo Subprograma
                        return optional($record->orcamentoPrograma->orcamento)->valor ?? '-';
                    }),

                Tables\Columns\TextColumn::make('orcamento_programa_valor')
                    ->label('Orçamento Restante')
                    ->numeric()
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
