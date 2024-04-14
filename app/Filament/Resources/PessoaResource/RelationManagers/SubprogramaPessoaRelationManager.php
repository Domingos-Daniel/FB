<?php

namespace App\Filament\Resources\PessoaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubprogramaPessoaRelationManager extends RelationManager
{
    protected static ?string $title = 'Programas & Subprogramas Associados a este beneficiario';
    protected static string $relationship = 'SubprogramaPessoa';
    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('programa.nome')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nome')
            ->columns([
                Tables\Columns\TextColumn::make('pessoa.nome')
                    ->sortable()
                    ->label('Beneficiário'),
                Tables\Columns\TextColumn::make('programa.nome')
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->label('Programa Social'),
                Tables\Columns\TextColumn::make('subprograma.designacao')
                    ->badge()
                    ->color('info')
                    ->label('Subprograma')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subprograma.valor')
                    ->badge()
                    ->color('info')
                    ->numeric()
                    ->money('AOA', divideBy: 100)
                    ->label('Orçamento')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_inicio')
                    ->date(format: 'd/m/Y')
                    ->sortable()
                    ->label('Data de Inicio'),
                Tables\Columns\TextColumn::make('data_fim')
                    ->date(format: 'd/m/Y')
                    ->sortable()
                    ->label('Data de Expiração'),
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
