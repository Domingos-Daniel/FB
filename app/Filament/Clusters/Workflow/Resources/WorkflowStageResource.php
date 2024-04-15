<?php

namespace App\Filament\Clusters\Workflow\Resources;

use App\Filament\Clusters\Workflow;
use App\Filament\Clusters\Workflow\Resources\WorkflowStageResource\Pages;
use App\Filament\Clusters\Workflow\Resources\WorkflowStageResource\RelationManagers;
use App\Models\WorkflowStage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkflowStageResource extends Resource
{
    protected static ?string $model = WorkflowStage::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Workflow::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')->label('Nome'),
                Forms\Components\Textarea::make('descricao')->label('Descrição'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')->label('Nome'),
                Tables\Columns\TextColumn::make('descricao')->label('Descrição'),
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
            'index' => Pages\ListWorkflowStages::route('/'),
            'create' => Pages\CreateWorkflowStage::route('/create'),
            'edit' => Pages\EditWorkflowStage::route('/{record}/edit'),
        ];
    }
}
