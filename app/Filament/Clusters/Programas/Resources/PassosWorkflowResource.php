<?php

namespace App\Filament\Clusters\Programas\Resources;

use App\Filament\Clusters\Programas;
use App\Filament\Clusters\Programas\Resources\PassosWorkflowResource\Pages;
use App\Filament\Clusters\Programas\Resources\PassosWorkflowResource\RelationManagers;
use App\Models\PassosWorkflow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PassosWorkflowResource extends Resource
{
    protected static ?string $model = PassosWorkflow::class;

    protected static ?string $modelLabel = 'Passos do Workflow';
    protected static ?string $pluralModelLabel = 'Passos dos Workflows';
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    
    protected static ?string $navigationGroup = 'Gestão Orçamental';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $cluster = Programas::class; 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->label("Nome do Passo")
                    ->required(),
                Forms\Components\RichEditor::make('descricao')
                    ->required()
                    ->maxLength(255)
                    ->label('Descrição do passo'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descricao')
                    ->limit(30)
                    ->label('Descricão')
                    ->html() 
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(format: 'd/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPassosWorkflows::route('/'),
            'create' => Pages\CreatePassosWorkflow::route('/create'),
            'view' => Pages\ViewPassosWorkflow::route('/{record}'),
            'edit' => Pages\EditPassosWorkflow::route('/{record}/edit'),
        ];
    }
}
