<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramaPessoaResource\Pages;
use App\Filament\Resources\ProgramaPessoaResource\RelationManagers;
use App\Models\Pessoa;
use App\Models\Programa;
use App\Models\ProgramaPessoa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class ProgramaPessoaResource extends Resource
{
    protected static ?string $model = ProgramaPessoa::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $programas = Programa::pluck('nome', 'id')->toArray();
        $pessoas = Pessoa::pluck('nome', 'id')->toArray();

        return $form
            ->schema([
                Forms\Components\Select::make('programa_id')
                    ->options($programas)
                    ->required(),
                Forms\Components\Select::make('pessoa_id')
                    ->options($pessoas)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pessoa.nome')
                    ->label('Pessoa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('programa.nome')
                    ->label('Programa Associado')
                    ->sortable(),
                Tables\Columns\TextColumn::make('programa.data_inicio')
                    ->label('Data de Início')
                    ->dateTime(format: 'd/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('programa.data_fim')
                    ->label('Data de Fim')
                    ->dateTime(format: 'd/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('programa.status')
                    ->label('Status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('programa.orcamento')
                    ->label('Orçamento do Programa')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListProgramaPessoas::route('/'),
            'create' => Pages\CreateProgramaPessoa::route('/create'),
            'edit' => Pages\EditProgramaPessoa::route('/{record}/edit'),
        ];
    }
}
