<?php

namespace App\Filament\Clusters\Programas\Resources\SubprogramaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramaRelationManager extends RelationManager
{
    protected static string $relationship = 'programa';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('designacao')
                    ->required()
                    ->maxLength(255),
                    
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('designacao')
            ->columns([
                Tables\Columns\TextColumn::make('orcamento.valor')
                    ->numeric(),
                Tables\Columns\TextColumn::make('programa.nome') 
                    ->label('Programa Associado')
                    ->searchable()
                    ->sortable(), 
                    Tables\Columns\TextColumn::make('subprograma.valor')
                    ->label('Valor do Subprograma')
                    ->numeric()
                    ->icon('heroicon-m-banknotes')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
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
