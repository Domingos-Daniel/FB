<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramaResource\Pages;
use App\Filament\Resources\ProgramaResource\RelationManagers;
use App\Models\Programa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramaResource extends Resource
{
    protected static ?string $model = Programa::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descricao')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('area_foco')->options([
                        'educacao',
                        'saude', 
                        'infraestrutura',
                    ])
                    ->required()
                    ->multiple(),
                Forms\Components\Select::make('publico_alvo')->options([
                        'estudantes',
                        'empresa',
                    ])
                    ->required()
                    ->preload()
                    ->multiple(),
                Forms\Components\Textarea::make('objetivo')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('metas')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('orcamento')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('data_inicio')
                    ->required(),
                Forms\Components\DatePicker::make('data_fim')
                    ->required(),
                Forms\Components\TextInput::make('responsavel')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')->options([
                        'pendente',
                        'visto',
                        'aprovado',
                        'reprovado',
                        'expirado',
                    ])->required()
                    ->preload(),
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
                Tables\Columns\TextColumn::make('orcamento')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_fim')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('responsavel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
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
            'index' => Pages\ManageProgramas::route('/'),
        ];
    }
}
