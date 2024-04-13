<?php

namespace App\Filament\Resources\ProgramaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubprogramaRelationManager extends RelationManager
{
    protected static ?string $title = 'Subprogramas Associados';
    protected static string $relationship = 'subprograma';

    public function form(Form $form): Form
    {
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
            ->recordTitleAttribute('designacao')
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()
                    ->label('ID do SubPrograma')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('designacao')
                    ->sortable()
                    ->label('Designação do Subprograma'),
                Tables\Columns\TextColumn::make('valor')
                    ->sortable()
                    ->badge()
                    ->money('AOA', divideBy: 100)
                    ->label('Orçamento do Subprograma'),
                
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
