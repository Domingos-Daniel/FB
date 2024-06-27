<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Programas\Resources\SubprogramaResource\Widgets\SubprogramaOverview;
use App\Filament\Exports\SubprogramaPessoaExporter;
use App\Filament\Resources\PessoaResource\RelationManagers\SubprogramaPessoaRelationManager;
use App\Filament\Resources\SubprogramaPessoaResource\Pages;
use App\Filament\Resources\SubprogramaPessoaResource\RelationManagers;
use App\Filament\Resources\SubprogramaPessoaResource\Widgets\SubprogramaPessoaOverview;
use App\Models\Orcamento;
use App\Models\Pessoa;
use App\Models\Programa;
use App\Models\Subprograma;
use App\Models\SubprogramaPessoa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Infolist;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class SubprogramaPessoaResource extends Resource
{
    protected static ?string $model = SubprogramaPessoa::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $modelLabel = 'Financiamento';
    protected static ?string $pluralModelLabel = 'Gestão de Financiamentos';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {

        $subprogramas = Subprograma::pluck('designacao', 'id')->toArray();
        $programas = Programa::pluck('nome', 'id')->toArray();
        $pessoas = Pessoa::pluck('nome', 'id')->toArray();
        $record = Subprograma::find(1);

        return $form
            ->schema([
                Forms\Components\Select::make('id_programa')
                    ->options($programas)
                    ->required(fn (string $context): bool => $context === 'create')
                    ->native(false)
                    ->suffixIcon('heroicon-m-folder')
                    ->suffixIconColor('info')
                    ->searchable()
                    ->preload()
                    ->label('Selecione o Programa Social')
                    ->live(),
                Forms\Components\Select::make('id_subprograma')
                    ->options(
                        function (Get $get) {
                            $selectedPrograma = $get('id_programa');
                            // Consulta para obter os subprogramas associados ao programa selecionado
                            $subprogramasAssociados = Subprograma::where('id_programa', $selectedPrograma)->pluck('designacao', 'id')->toArray();
                            return $subprogramasAssociados;
                        }
                    )
                    ->searchable()
                    ->native(false)
                    ->suffixIcon('heroicon-m-folder')
                    ->suffixIconColor('info')
                    ->label("Selecione o SubPrograma")
                    ->preload()
                    ->hidden(fn (Get $get) => empty($get('id_programa')))
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Select::make('id_pessoa')
                    ->options($pessoas)
                    ->hidden(fn (Get $get) => empty($get('id_programa')))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->suffixIcon('heroicon-m-user')
                    ->suffixIconColor('info')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->label('Selecione o Beneficiário'),
                Forms\Components\DatePicker::make('data_inicio')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->hidden(fn (Get $get) => empty($get('id_programa')))
                    ->suffixIcon('heroicon-m-calendar')
                    ->suffixIconColor('info')
                    ->label('Data de Inicio'),
                Forms\Components\DatePicker::make('data_fim')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->native(false)
                    ->hidden(fn (Get $get) => empty($get('id_programa')))
                    ->closeOnDateSelection()
                    ->label('Data de Fim')
                    ->suffixIcon('heroicon-m-calendar')
                    ->suffixIconColor('info')
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('description')
                    ->label('Descrição')
                    ->maxLength(65535)
                    ->hidden(fn (Get $get) => empty($get('id_programa')))
                    ->columnSpanFull()
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Hidden::make('id_criador')
                    ->default(auth()->id()),

            ]);
    }

    public static function table(Table $table): Table
    {
        $subprogramas = Subprograma::pluck('designacao', 'id')->toArray();
        $programas = Programa::pluck('nome', 'id')->toArray();
        $pessoas = Pessoa::pluck('nome', 'id')->toArray();
        $record = Subprograma::find(1);

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pessoa.nome')
                    ->sortable()
                    ->label('Beneficiário'),
                Tables\Columns\TextColumn::make('programa.nome')
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->label('Programa Social'),
                Tables\Columns\TextColumn::make('subprograma.designacao')
                    ->badge()
                    ->color('info')
                    ->label('Subprograma')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_inicio')
                    ->date(format: 'd/m/Y')
                    ->sortable()
                    ->label('Data de Inicio'),
                Tables\Columns\TextColumn::make('data_fim')
                    ->date(format: 'd/m/Y')
                    ->sortable()
                    ->label('Data de Expiração'),
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
                                fn (Builder $query, $date): Builder => $query->whereDate('data_inicio', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_inicio', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('programa')
                    ->label('Tipo de Programa Social')
                    ->multiple()
                    ->relationship('programa', 'nome')
                    ->preload(),

                Tables\Filters\SelectFilter::make('subprograma')
                    ->label('Tipo de Subprograma')
                    ->multiple()
                    ->relationship('subprograma', 'designacao')
                    ->preload(),
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
                        ->exporter(SubprogramaPessoaExporter::class)
                        ->columnMapping(true)
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Seção de Informações do Beneficiário e Programa Social
                Components\Split::make([
                    Components\Section::make('Informações')
                        ->schema([
                            Components\TextEntry::make('pessoa.nome')
                                ->badge()
                                ->label('Beneficiário'),
                            Components\TextEntry::make('programa.nome')
                                ->badge()
                                ->color('primary')
                                ->label('Programa Social'),
                            Components\TextEntry::make('subprograma.designacao')
                                ->badge()
                                ->color('info'),
                            Components\TextEntry::make('created_at')
                                ->badge()
                                ->label('Criado em')
                                ->dateTime(),
                        ])->grow(true),
                ])->from('md'),

                // Seção de Datas
                Components\Split::make([
                    Components\Section::make('Datas')
                        ->schema([
                            Components\TextEntry::make('data_inicio')
                                ->badge()
                                ->label('Data de Início')
                                ->dateTime(format: 'd/m/Y'),
                            Components\TextEntry::make('data_fim')
                                ->badge()
                                ->label('Data de Expiração')
                                ->dateTime(format: 'd/m/Y'),

                            Components\TextEntry::make('updated_at')
                                ->badge()
                                ->label('Atualizado em')
                                ->dateTime(),
                        ])->grow(true),
                ])->from('xl'),

            ]);
    }


    public static function getRelations(): array
    {
        return [
            SubprogramaPessoaRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            SubprogramaPessoaOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubprogramaPessoas::route('/'),
            'create' => Pages\CreateSubprogramaPessoa::route('/create'),
            'view' => Pages\ViewSubprogramaPessoa::route('/{record}'),
            'edit' => Pages\EditSubprogramaPessoa::route('/{record}/edit'),
        ];
    }
}
