<?php

namespace App\Filament\Clusters\Programas\Resources;

use App\Filament\Clusters\Programas;
use App\Filament\Clusters\Programas\Resources\OrcamentoGeralResource\Pages;
use App\Filament\Clusters\Programas\Resources\OrcamentoGeralResource\RelationManagers;
use App\Models\OrcamentoGeral;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrcamentoGeralResource extends Resource
{
    protected static ?string $model = OrcamentoGeral::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    protected static ?string $navigationGroup = 'Gestão Orçamental';

    
    protected static ?string $modelLabel = 'Orçamento Anual';
    protected static ?string $pluralModelLabel = 'Orçamentos Anuais';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count(); 
    }

    protected static ?string $cluster = Programas::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('nome')
                    ->required()
                    ->label('Nome do Orçamento')
                    ->maxLength(255),
                Forms\Components\TextInput::make('valor_total')
                    ->required()
                    ->prefixIcon('heroicon-o-currency-dollar')
                    ->prefixIconColor('success')
                    ->label('Valor Orçamento Geral')
                    ->numeric(), 
                Forms\Components\RichEditor::make('descricao')
                    ->required()
                    ->columnSpanFull()
                    ->label('Descrição do Orçamento')
                    ->maxLength(255),
                Forms\Components\Hidden::make('id_criador')
                        ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome do Orçamento')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descricao')
                    ->label('Descrição do Orçamento')
                    ->html()
                    ->limit(20),
                Tables\Columns\TextColumn::make('valor_total')
                    ->label('Valor Orçamento Geral')
                    ->money('USD', true)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOrcamentoGerals::route('/'),
        ];
    }
}
