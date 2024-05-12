<?php

namespace App\Filament\Clusters\Programas\Resources;

use App\Filament\Clusters\Programas;
use App\Filament\Clusters\Programas\Resources\OrcamentoResource\Pages;
use App\Filament\Clusters\Programas\Resources\OrcamentoResource\RelationManagers;
use App\Models\Orcamento;
use App\Models\Programa;
use App\Models\WorkflowItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;

class OrcamentoResource extends Resource
{
    protected static ?string $model = Orcamento::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Orçamento';
    protected static ?string $pluralModelLabel = 'Orçamentos';
    protected static ?string $navigationGroup = 'Gestão Orcamental';
    protected static ?string $cluster = Programas::class;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count(); 
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()->whereHas('workflowItem', function (Builder $query) {
    //         $query->where('etapa_atual_id', 2);
    //     });
    // }

    // public function obterRegistrosAprovados()
    // {
    //     // Consulta para recuperar registros aprovados no workflow
    //     $registrosAprovados = Orcamento::where('etapa_atual_id', 3)->get();
    
    //     // Faça algo com os registros aprovados...
    // }

    public static function form(Form $form): Form
    {
        $programas = Programa::pluck('nome', 'id')->toArray();

        return $form
            ->schema([ 
                 // Forms\Components\TextInput::make('id_programa')
                //     ->required()
                //     ->numeric(),
                // Forms\Components\Select::make('programa_id')
                //     ->options($programas)
                //     ->label("Selecione o Programa Social")
                //     ->searchable()
                //     ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\TextInput::make('valor')
                    ->label("Valor do Orçamento")
                    ->required()
                    ->numeric(),
                Forms\Components\RichEditor::make('descricao')
                    ->required()
                    ->maxLength(255)
                    ->label('Descrição do Orçamento'),
                Forms\Components\Hidden::make('id_criador')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        //$registrosAprovados = WorkflowItem::where('etapa_atual_id', 3)->get();
        return $table
        
            ->columns([
                Tables\Columns\TextColumn::make('descricao')
                    ->label('Descrição')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('valor')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListOrcamentos::route('/'),
            'create' => Pages\CreateOrcamento::route('/create'),
            'view' => Pages\ViewOrcamento::route('/{record}'),
            'edit' => Pages\EditOrcamento::route('/{record}/edit'),
        ];
    }

   
}
