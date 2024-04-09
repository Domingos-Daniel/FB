<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Programas;
use App\Filament\Exports\ProgramaExporter;
use App\Filament\Resources\ProgramaResource\Pages;
use App\Filament\Resources\ProgramaResource\RelationManagers;
use App\Models\Programa;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class ProgramaResource extends Resource
{
    protected static ?string $model = Programa::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = Programas::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    
    protected static ?string $modelLabel = 'Programa Social';
    protected static ?string $pluralModelLabel = 'Gestão de Programas';

    protected static ?string $recordTitleAttribute = 'nome';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 Forms\Components\TextInput::make('nome')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255),
                Forms\Components\Textarea::make('descricao')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('area_foco')
                    ->options([
                        'Educação' => 'Educação',
                        'Saúde' => 'Saúde',
                        'Infraestrutura' => 'Infraestrutura',
                    ])
                    //->multiple()
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Select::make('publico_alvo')
                    ->options([
                        'Estudantes' => 'Estudantes',
                        'Empresa' => 'Empresa',
                    ])
                    ->required(fn (string $context): bool => $context === 'create')
                    //->multiple()
                    ->preload(),
                Forms\Components\Textarea::make('objetivo')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('metas')
                    ->maxLength(65535)
                    ->columnSpanFull(), 

                // Forms\Components\TextInput::make('orcamento')
                //     ->required(fn (string $context): bool => $context === 'create')
                //     ->numeric(),

                // Forms\Components\Select::make('descricao')
                //     ->relationship('orcamentos', 'descricao')
                //     ->multiple()
                //     ->required()
                //     ->preload()->label('Descrição do Orcamento'), 

                // Forms\Components\TextInput::make('responsavel')
                //     ->required(fn (string $context): bool => $context === 'create')
                //     ->maxLength(255), 
                Forms\Components\Select::make('responsavel')
                ->required(fn (string $context): bool => $context === 'create')
                ->options(
                    User::whereHas('roles', function ($query) {
                        $query->where('name', 'manager');
                    })->pluck('name', 'id')
                )
                ->label('Responsável')
                ->searchable(),
            ]); 
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('area_foco')
                    ->searchable(),
                Tables\Columns\TextColumn::make('publico_alvo')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('orcamento')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('responsavel')
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                    ->label('Exportar Dado(s)')
                    ->exporter(ProgramaExporter::class)
                    ->columnMapping(true)
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
            'index' => Pages\ListProgramas::route('/'),
            'create' => Pages\CreatePrograma::route('/create'),
            'view' => Pages\ViewPrograma::route('/{record}'),
            'edit' => Pages\EditPrograma::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->nome;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nome', 'responsavel'];
    }
}
