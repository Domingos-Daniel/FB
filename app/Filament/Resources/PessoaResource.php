<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PessoaExporter;
use App\Filament\Resources\PessoaResource\Pages;
use App\Filament\Resources\PessoaResource\RelationManagers;
use App\Models\Pessoa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PessoaResource extends Resource
{
    protected static ?string $model = Pessoa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $recordTitleAttribute = 'nome';
    
    protected static ?string $modelLabel = 'Beneficiario';
    //protected static ?int $navigationSort = 1;
    protected static ?string $pluralModelLabel = 'Gestão dos Beneficiarios';
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
            Forms\Components\TextInput::make('email')
                ->email()
                ->required(fn (string $context): bool => $context === 'create')
                ->maxLength(255),
            Forms\Components\TextInput::make('bi')
                ->label('BI / Nº de Identificação Fiscal')
                ->required(fn (string $context): bool => $context === 'create')
                ->maxLength(14)
                ->minLength(14),
            Forms\Components\DatePicker::make('data_nascimento')
                ->required(fn (string $context): bool => $context === 'create')
                ->date()
                ->label('Data de Nascimento / Data de Criação')
                ->closeOnDateSelection()
                ->minDate(now()->subYears(80))
                ->maxDate(now()->subYears(20)),
            Forms\Components\Radio::make('genero')
                ->label('Género')
                ->options([
                    'Masculino' => 'Masculino',
                    'Feminino' => 'Feminino',
                    'Outro' => 'Outro',
                ])
                ->required(fn (string $context): bool => $context === 'create'),
            Forms\Components\Select::make('grau_academico')->options([
                    'Ensino Geral' => 'Ensino Geral',
                    'Ensino Medio' =>'Ensino Medio',
                    'Bacharel'=>'Bacharel',
                    'Licenciado'=>'Licenciado',
                    'Msc'=>'Msc',
                    'PHD'=>'PHD',
                    'Outro'=>'Outro',
                ])
                ->required(fn (string $context): bool => $context === 'create')
                ->searchable()
                ->preload(),
             Forms\Components\Select::make('tipo_pessoa')->options([
                    'Individual' => 'Individual',
                    'Institucional' =>'Institucional',
                    'Empresa' =>'Empresa',
                ])
                ->required(fn (string $context): bool => $context === 'create')
                ->searchable()
                ->preload(),

            Forms\Components\Textarea::make('morada')
                ->label('Morada / Endereço')
                ->required(fn (string $context): bool => $context === 'create')
                ->maxLength(65535)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('telefone')
                ->label('Telefone / Celular')
                ->tel()
                ->telRegex('/^\d{9}$/')
                ->required(fn (string $context): bool => $context === 'create')
                ->numeric()
                ->maxLength(9)
                ->minLength(9),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('nome')
                ->searchable()
                ->label('Beneficiário')
                ->sortable(),
            Tables\Columns\TextColumn::make('email')
                ->searchable(),
            Tables\Columns\TextColumn::make('bi')
                ->searchable(),
            Tables\Columns\TextColumn::make('tipo_pessoa')
                ->label('Tipo Beneficiário')
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                    ->label('Exportar Dado(s)')
                    ->exporter(PessoaExporter::class)
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
            'index' => Pages\ListPessoas::route('/'),
            'create' => Pages\CreatePessoa::route('/create'),
            'view' => Pages\ViewPessoa::route('/{record}'),
            'edit' => Pages\EditPessoa::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->nome;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nome', 'email', 'morada', 'telefone'];
    }
}
