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
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

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
                    ->native(false)
                    ->label("Selecione o Programa Social")
                    ->preload()
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\TextInput::make('designacao')
                    ->label("Designação")
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('valor')
                    ->required()
                    ->numeric(),



            ]);
    }

    public function orcamentoPrograma()
    {
        return $this->belongsTo(OrcamentoPrograma::class, 'id_programa', 'id_programa'); // Assuming foreign key is id_programa
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('orcamento_programa_valor_original')
                    ->label('Orçamento Original do Programa')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // Acessar o valor original do orçamento do programa a partir da relação definida no modelo Subprograma
                        return optional($record->orcamentoPrograma->orcamento)->valor ?? '-';
                    }),
                
                Tables\Columns\TextColumn::make('orcamento_programa_valor')
                    ->label('Orçamento Restante')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // Acessar o valor do orçamento do programa a partir da relação definida no modelo Subprograma
                        $orcamentoProgramaValor = optional($record->orcamentoPrograma->orcamento)->valor ?? 0;

                        // Obtendo o valor do subprograma
                        $valorSubprograma = $record->valor;

                        // Calculando a diferença entre o valor da tabela orcamento e o valor do subprograma
                        $diferenca = $orcamentoProgramaValor - $valorSubprograma;

                        return $diferenca;
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
