<?php

namespace App\Filament\Resources\ProgramaPessoaResource\RelationManagers;

use App\Models\Pessoa;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Programa;


class PessoaRelationManager extends RelationManager
{
    protected static string $relationship = 'Pessoa';
    protected static ?string $title = 'Beneficiario/Programa';

    public function form(Form $form): Form
    {    
        $programas = Programa::pluck('nome', 'id')->toArray();
        $pessoas = Pessoa::pluck('nome', 'id')->toArray();

        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nome')
            ->columns([
                Tables\Columns\TextColumn::make('nome'),
                Tables\Columns\TextColumn::make('programa.nome')
                    ->label('Programa Associado')
                    ->sortable(),
                    Tables\Columns\TextColumn::make('programa.orcamento')
                    ->label('Orçamento do Programa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('programa.data_inicio')
                    ->label('Data de Início')
                    ->dateTime(format: 'd/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('programa.data_fim')
                    ->label('Data de Fim')
                    ->dateTime(format: 'd/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
