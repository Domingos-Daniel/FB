<?php

namespace App\Filament\Clusters\Programas\Resources;

use App\Filament\Clusters\Programas;
use App\Filament\Clusters\Programas\Resources\OrcamentoprogramaResource\Pages;
use App\Filament\Clusters\Programas\Resources\OrcamentoprogramaResource\RelationManagers;
use App\Models\Orcamentoprograma;
use App\Models\Programa;
use App\Models\Orcamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrcamentoprogramaResource extends Resource
{
    protected static ?string $model = Orcamentoprograma::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    protected static ?string $navigationParentItem = 'Orcamento';
    protected static ?string $modelLabel = 'Atribuição';
    protected static ?string $pluralModelLabel = 'Atribuir Orçamentos'; //Gestão de Orçamentos;

    protected static ?string $navigationGroup = 'Gestão Orcamental';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Programas::class;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        $programas = Programa::pluck('nome', 'id')->toArray();
        $orcamentos = Orcamento::pluck('valor', 'id')->toArray();
        return $form
            ->schema([
                Forms\Components\Select::make('id_programa')
                    ->options($programas)
                    ->searchable()
                    ->label("Selecione o Programa Social")
                    ->preload()
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Select::make('id_orcamento')
                    ->options($orcamentos)
                    ->label("Selecione o Orçamento")
                    ->preload()
                    ->searchable()
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Hidden::make('id_criador')
                    ->default(auth()->id()),
                // Forms\Components\TextInput::make('id_programa')
                //     ->required()
                //     ->numeric(),
                // Forms\Components\TextInput::make('id_orcamento')
                //     ->required()
                //     ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_programa')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('programa.nome')
                    ->label('Programa Associado')
                    ->sortable(),
                Tables\Columns\TextColumn::make('orcamento.valor')
                    ->label('Orçamento do Programa')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('id_orcamento')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListOrcamentoprogramas::route('/'),
            'create' => Pages\CreateOrcamentoprograma::route('/create'),
            'view' => Pages\ViewOrcamentoprograma::route('/{record}'),
            'edit' => Pages\EditOrcamentoprograma::route('/{record}/edit'),
        ];
    }
}
