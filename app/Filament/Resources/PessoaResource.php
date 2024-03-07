<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PessoaResource\Pages;
use App\Filament\Resources\PessoaResource\RelationManagers;
use App\Models\Pessoa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Radio;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PessoaResource extends Resource
{
    protected static ?string $model = Pessoa::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('bi')
                    ->required()
                    ->maxLength(14)
                    ->minLength(14),
                Forms\Components\DatePicker::make('data_nascimento')
                    ->required()
                    ->closeOnDateSelection(),
                Forms\Components\Radio::make('genero')
                    ->options([
                        'Masculino' => 'Masculino',
                        'Feminino' => 'Feminino',
                        'Outro' => 'Outro',
                    ])
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('grau_academico')->options([
                    'Ensino Geral',
                    'Ensino Medio',
                    'Bacharel',
                    'Licenciado',
                    'Msc',
                    'PHD',
                ])->required()
                
                ->preload(),
                Forms\Components\Textarea::make('morada')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('telefone')
                    ->tel()
                    ->required()
                    ->numeric()
                    ->maxLength(15)
                    ->minLength(9),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_nascimento')
                    ->date(format: 'd/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('genero')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grau_academico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefone')
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
            'index' => Pages\ManagePessoas::route('/'),
        ];
    }
}
