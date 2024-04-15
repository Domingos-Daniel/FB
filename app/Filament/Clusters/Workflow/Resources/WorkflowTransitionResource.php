<?php

namespace App\Filament\Clusters\Workflow\Resources;

use App\Filament\Clusters\Workflow;
use App\Filament\Clusters\Workflow\Resources\WorkflowTransitionResource\Pages;
use App\Filament\Clusters\Workflow\Resources\WorkflowTransitionResource\RelationManagers;
use App\Models\WorkflowStage;
use App\Models\WorkflowTransition;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkflowTransitionResource extends Resource
{
    protected static ?string $model = WorkflowTransition::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Workflow::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('etapa_origem_id')
                    ->relationship('workflowStage', 'nome')
                    ->label('Etapa Origem')
                    ->native(false)
                    ->live(),

                Forms\Components\Select::make('etapa_destino_id')
                    ->label('Etapa Destino')
                    ->native(false)
                    ->relationship('workflowStage', 'nome'),

                Forms\Components\Select::make('permissao_requerida')
                    ->relationship('roles', 'name', function (Builder $query) {
                        return auth()->user()->hasRole('Admin') ? $query : $query->where('name', '!=', 'Admin');
                    })
                    ->multiple()
                    ->searchable()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->preload(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('etapa_origem_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('etapa_destino_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissao_requerida')
                    ->searchable(),
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
            'index' => Pages\ListWorkflowTransitions::route('/'),
            'create' => Pages\CreateWorkflowTransition::route('/create'),
            'edit' => Pages\EditWorkflowTransition::route('/{record}/edit'),
        ];
    }
}
