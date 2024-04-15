<?php

namespace App\Filament\Clusters\Workflow\Resources;

use App\Filament\Clusters\Workflow;
use App\Filament\Clusters\Workflow\Resources\WorkflowItemResource\Pages;
use App\Filament\Clusters\Workflow\Resources\WorkflowItemResource\RelationManagers;
use App\Models\WorkflowItem;
use App\Models\WorkflowStage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Forms\Components;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class WorkflowItemResource extends Resource
{
    protected static ?string $model = WorkflowItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    protected static ?string $cluster = Workflow::class;

    public static function form(Form $form): Form
    {
        // Obtenha todos os modelos disponíveis e suas classes
        $models = \Illuminate\Support\Facades\File::allFiles(app_path('Models'));
        $modelOptions = [];
        foreach ($models as $model) {
            $className = 'App\\Models\\' . pathinfo($model->getFilename(), PATHINFO_FILENAME);
            $modelOptions[$className] = $className;
        }

        // $models2 = \Illuminate\Support\Facades\File::allFiles(app_path('Models'));
        // $modelOptions2 = [];
        // foreach ($models2 as $model2) {
        //     $className2 = 'App\\Models\\' . pathinfo($model->getFilename(), PATHINFO_FILENAME);
        //     $modelName2 = class_basename($className2); // Obtém apenas o nome do modelo sem o namespace
        //     $modelOptions2[$modelName2] = $modelName2;
        // }

        return $form
            ->schema([
                Forms\Components\Select::make('modelo_type')
                    ->label('Modelo')
                    ->options($modelOptions),
                Forms\Components\Select::make('etapa_atual_id')
                    ->label('Etapa Atual')
                    ->options(function () {
                        return \App\Models\WorkflowStage::pluck('nome', 'id');
                    })
                    ->disabled(function ($record) {
                        return $record && $record->etapa_atual_id == 1;
                    })
            
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
          
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('modelo_type')->label('Modelo'),
                Tables\Columns\TextColumn::make('workflow_stag')
                ->label('Etapa Atual')
                ->getStateUsing(function ($record) {
                    return $record->workflowStage->nome; // Supondo que a relação seja chamada "workflowStage" e o atributo com o nome seja "nome"
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('avancar_etapa')
                    ->label('Avançar Etapa')
                    ->visible(function (WorkflowItem $record) {
                        return $record->usuarioPodeAvancar();
                    }),
                \Filament\Tables\Actions\Action::make('retroceder_etapa')
                    ->label('Retroceder Etapa')
                    ->visible(function (WorkflowItem $record) {
                        // Evite retroceder da primeira etapa
                        return $record->etapa_atual_id !== 1;
                    }),
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
            'index' => Pages\ListWorkflowItems::route('/'),
            'create' => Pages\CreateWorkflowItem::route('/create'),
            'edit' => Pages\EditWorkflowItem::route('/{record}/edit'),
        ];
    }
}
