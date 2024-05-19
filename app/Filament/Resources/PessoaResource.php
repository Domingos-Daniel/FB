<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PessoaExporter;
use App\Filament\Resources\PessoaResource\Pages;
use App\Filament\Resources\PessoaResource\RelationManagers;
use App\Filament\Resources\PessoaResource\RelationManagers\SubprogramaPessoaRelationManager;
use App\Filament\Resources\SubprogramaPessoaResource\RelationManagers\PessoaRelationManager;
use App\Models\Pessoa;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Infolist;

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
                ->label('Nome do Beneficiario')
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
                Forms\Components\Select::make('grau_academico')
                    ->options([
                        'Ensino Geral' => 'Ensino Geral',
                        'Ensino Medio' => 'Ensino Medio',
                        'Bacharel' => 'Bacharel',
                        'Licenciado' => 'Licenciado',
                        'Msc' => 'Msc',
                        'PHD' => 'PHD',
                        'Outro' => 'Outro',
                    ])
                    ->required(fn (string $context): bool => $context === 'create')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('tipo_pessoa')->options([
                    'Individual' => 'Individual',
                    'Institucional' => 'Institucional',
                    'Empresa' => 'Empresa',
                ])
                    ->required(fn (string $context): bool => $context === 'create')
                    ->searchable()
                    ->label('Tipo de Beneficiario')
                    ->columnSpanFull()
                    ->preload(),

                Forms\Components\Textarea::make('morada')
                    ->label('Morada / Endereço')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(100)
                    ->rows(5)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('telefone')
                    ->label('Telefone / Celular')
                    ->tel()
                    ->telRegex('/^\d{9}$/')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->numeric()
                    ->maxLength(9) 
                    ->minLength(9),
                Forms\Components\Hidden::make('id_criador')
                    ->default(auth()->id()),
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
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_nascimento')
                    ->date(format: 'd/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('genero')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grau_academico')
                    ->searchable()
                    ->badge()
                    ->color('primary'),
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
                Tables\Filters\Filter::make('created_at')
                    ->label('Intervalo de Data de Criação')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Intervalo de Data de Criação Inicio'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Intervalo de Data de Criação Fim'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('data_nascimento')
                    ->label('Data de Nascimento')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Data de Nascimento Inicial'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Data de Nascimento Final'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_nascimento', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_nascimento', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('tipo_pessoa')
                    ->label('Tipo de Beneficiário')
                    ->multiple()
                    ->options([
                        'Individual' => 'Individual',
                        'Institucional' => 'Institucional',
                        'Empresa' => 'Empresa',
                    ]),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                Components\Split::make([
                    // Seção de Informações Pessoais
                    Components\Section::make('Informações Pessoais')
                        ->schema([
                            Components\TextEntry::make('nome')
                                ->badge()
                                ->label('Nome')
                                ->weight(FontWeight::Bold)
                                ->color('info'),
                            Components\TextEntry::make('email')
                                ->badge()
                                ->label('Email')
                                ->color('info'),
                            Components\TextEntry::make('bi')
                                ->badge()
                                ->label('BI / Nº de Identificação Fiscal')
                                ->color('info'),
                            Components\TextEntry::make('data_nascimento')
                                ->badge()
                                ->label('Data de Nascimento')
                                ->color('info'),
                            Components\TextEntry::make('genero')
                                ->badge()
                                ->label('Gênero')
                                ->color('info'),
                            Components\TextEntry::make('grau_academico')
                                ->badge()
                                ->label('Grau Acadêmico')
                                ->color('info'),
                            Components\TextEntry::make('telefone')
                                ->badge()
                                ->label('Telefone / Celular')
                                ->color('info'),
                        ])->grow(true),
                ]),

                // Seção de Endereço
                Components\Split::make([
                    Components\Section::make('Seção de Endereço')->schema([

                        Components\TextEntry::make('morada')
                            ->badge()
                            ->label('Morada / Endereço')
                            ->color('info'),

                        Components\TextEntry::make('created_at')
                            ->badge()
                            ->label('Criado em')
                            ->color('info'),
                        Components\TextEntry::make('updated_at')
                            ->badge()
                            ->label('Atualizado em')
                            ->color('info'),
                    ]),
                ])
            ]);
    }


    public static function getRelations(): array
    {
        return [
            SubprogramaPessoaRelationManager::class,
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

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return PessoaResource::getUrl('view', ['record' => $record]);
    }
}
