<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Programas;
use App\Filament\Clusters\Programas\Resources\SubprogramaResource\RelationManagers\ProgramaRelationManager;
use App\Filament\Exports\ProgramaExporter;
use App\Filament\Resources\ProgramaResource\Pages;
use App\Filament\Resources\ProgramaResource\RelationManagers\SubprogramaRelationManager;
use App\Models\gasto;
use App\Models\Orcamento;
use App\Models\OrcamentoPrograma;
use App\Models\Programa;
use App\Models\Subprograma;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components;
use Filament\Support\Enums\FontWeight;

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
    public function orcamentoPrograma()
    {
        return $this->belongsTo(OrcamentoPrograma::class, 'id_programa', 'id_programa'); // Assuming foreign key is id_programa
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Nome Completo'),
                Forms\Components\RichEditor::make('descricao')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(65535)
                    ->label('Descrição')
                    ->columnSpanFull(),
                Forms\Components\Select::make('area_foco')
                    ->label('Area de Foco')
                    ->options([
                        'Educação' => 'Educação',
                        'Saúde' => 'Saúde',
                        'Infraestrutura' => 'Infraestrutura',
                    ])
                    ->multiple()
                    ->native(false)
                    ->preload()
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Select::make('publico_alvo')
                    ->options([
                        'Estudantes' => 'Estudantes',
                        'Empresa' => 'Empresa',
                    ])
                    ->label('Publico Alvo')
                    ->native(false) 
                    ->required(fn (string $context): bool => $context === 'create')
                    ->multiple()
                    ->preload(),
                Forms\Components\Textarea::make('objetivo')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(65535)
                    ->label('Objectivo do Programa')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('metas')
                    ->maxLength(65535)
                    ->label('Meta')
                    ->columnSpanFull(),
                Forms\Components\Select::make('responsavel')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->options(
                        User::whereHas('roles', function ($query) {
                            $query->where('name', 'manager');
                        })->pluck('name', 'name')
                    )
                    ->label('Responsável')
                    ->native(false)
                    ->preload()
                    ->searchable(),
                Forms\Components\Hidden::make('id_criador')
                    ->default(auth()->id()),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome Completo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('area_foco')
                    ->label('Area de Foco')
                    ->badge()
                    ->color('info')
                    ->listWithLineBreaks()
                    ->searchable(),
                Tables\Columns\TextColumn::make('publico_alvo')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->listWithLineBreaks()
                    ->label('Publico Alvo'),
                Tables\Columns\TextColumn::make('orcamento')
                    ->label('Orçamento')
                    ->badge()
                    ->color('success')
                    ->numeric()
                    ->money('USD', true)
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // Acessar o valor do orçamento associado ao programa
                        $orcamentoPrograma = OrcamentoPrograma::where('id_programa', $record->id)->first();

                        if ($orcamentoPrograma) {
                            $orcamento = Orcamento::find($orcamentoPrograma->id_orcamento);
                            return $orcamento ? $orcamento->valor : '-';
                        }

                        return '- Sem Orcamento Associado';
                    }),

                Tables\Columns\TextColumn::make('diferenca_orcamento_gasto')
                    ->label('Valor Restante')
                    ->numeric()
                    ->badge()
                    ->color(fn ($record) => $record->diferenca_orcamento_gasto > 100000 ? 'danger' : 'success')
                    ->sortable()
                    ->money('USD', true)
                    ->getStateUsing(function ($record) {
                        // Consulta o orçamento associado ao programa
                        $orcamentoPrograma = OrcamentoPrograma::where('id_programa', $record->id)->first();

                        if ($orcamentoPrograma) {
                            // Consulta o valor do orçamento
                            $orcamento = Orcamento::find($orcamentoPrograma->id_orcamento);

                            if ($orcamento) {
                                $valorOrcamento = $orcamento->valor;

                                // Consulta os gastos associados ao programa e calcula o total de gastos
                                $totalGasto = Gasto::where('id_programa', $record->id)->sum('valor_gasto');

                                // Calcula a diferença entre o valor do orçamento e o total de gastos
                                $diferenca = $valorOrcamento - $totalGasto;
                                return $diferenca;
                            }
                        }

                        return '- Sem Orcamento Associado';
                    }),

                Tables\Columns\TextColumn::make('numero_subprograma')
                    ->label('Numero de Subprogramas')
                    ->getStateUsing(function ($record) {
                        return Subprograma::where('id_programa', $record->id)->count();
                    })
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('responsavel')
                    ->searchable()
                    ->badge()
                    ->color('warning'),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Split::make([
                    Components\Section::make([
                        Components\TextEntry::make('nome')
                            ->label('Programa Social')
                            ->badge()
                            ->weight(FontWeight::Bold)
                            ->color('info')
                            ->copyable()
                            ->copyMessage('Copiado!')
                            ->copyMessageDuration(1500),
                        Components\TextEntry::make('area_foco')
                            ->badge()
                            ->label('Area de Foco')
                            ->separator(',')
                            ->color('success'),
                        Components\TextEntry::make('publico_alvo')
                            ->badge()
                            ->label('Publico Alvo')
                            ->color('success'),
                        Components\TextEntry::make('responsavel')
                            ->label('Responsável do Programa')
                            ->badge()
                            ->color('success'),
                        Components\TextEntry::make('numero_subprograma')
                            ->label('Numero de Subprogramas')
                            ->color('info')
                            ->badge()
                            ->getStateUsing(function ($record) {
                                return Subprograma::where('id_programa', $record->id)->count();
                            }),
                    ])->grow(true),
                ])->from('md'),

                Components\Split::make([

                    Components\Section::make([
                        Components\TextEntry::make('orcamento.valor')
                            ->numeric()
                            ->money('USD', true)
                            ->color(function ($record) {
                                // Verifica se o valor do orçamento está disponível no registro
                                if (isset($record['orcamento']['valor'])) {
                                    // Obtém o valor do orçamento do registro
                                    $valorOrcamento = $record['orcamento']['valor'];

                                    // Retorna 'success' (verde) se o valor do orçamento for maior que 10000, senão retorna 'danger' (vermelho)
                                    return $valorOrcamento > 1000000 ? 'success' : 'danger';
                                }

                                // Retorna uma cor padrão caso o valor do orçamento não esteja disponível
                                return 'danger';
                            })
                            ->money('USD', true)
                            ->badge()
                            ->label('Orçamento'),

                        Components\TextEntry::make('diferenca_orcamento_gasto')
                            ->numeric()
                            ->money('USD', true)
                            ->color(function ($record) {
                                // Verifica se o valor do orçamento está disponível no registro
                                if (isset($record['orcamento']['valor'])) {
                                    // Obtém o valor do orçamento do registro
                                    $valorOrcamento = $record['orcamento']['valor'];

                                    // Retorna 'success' (verde) se o valor do orçamento for maior que 10000, senão retorna 'danger' (vermelho)
                                    return $valorOrcamento > 1000000 ? 'success' : 'danger';
                                }

                                // Retorna uma cor padrão caso o valor do orçamento não esteja disponível
                                return 'danger';
                            })
                            ->badge()
                            ->getStateUsing(function ($record) {
                                // Consulta o orçamento associado ao programa
                                $orcamentoPrograma = OrcamentoPrograma::where('id_programa', $record->id)->first();

                                if ($orcamentoPrograma) {
                                    // Consulta o valor do orçamento
                                    $orcamento = Orcamento::find($orcamentoPrograma->id_orcamento);

                                    if ($orcamento) {
                                        $valorOrcamento = $orcamento->valor;

                                        // Consulta os gastos associados ao programa e calcula o total de gastos
                                        $totalGasto = Gasto::where('id_programa', $record->id)->sum('valor_gasto');

                                        // Calcula a diferença entre o valor do orçamento e o total de gastos
                                        $diferenca = $valorOrcamento - $totalGasto;
                                        return $diferenca;
                                    }
                                }

                                return '- Sem Orcamento Associado';
                            })
                            ->money('USD', true)
                            ->label('Orçamento Restando do Programa'),

                        Components\TextEntry::make('created_at')
                            ->dateTime(format: "d/m/Y H:i:s")
                            ->label('Criado em'),
                        Components\TextEntry::make('updated_at')
                            ->dateTime(format: "d/m/Y H:i:s")
                            ->label('Atualizado em'),
                    ])->grow(true),
                ]),

                Components\Section::make('Informações do Programa')
                    ->schema([
                        Components\TextEntry::make('descricao')
                            ->limit(250)
                            ->markdown()
                            ->label('Descrição')
                            ->tooltip(function (Components\TextEntry $component): ?string {
                                $state = $component->getState();

                                if (strlen($state) <= $component->getCharacterLimit()) {
                                    return null;
                                }

                                // Only render the tooltip if the entry contents exceeds the length limit.
                                return $state;
                            }),
                        Components\TextEntry::make('objetivo')
                            ->label('Objectivo')
                            ->tooltip(function (Components\TextEntry $component): ?string {
                                $state = $component->getState();

                                if (strlen($state) <= $component->getCharacterLimit()) {
                                    return null;
                                }

                                // Only render the tooltip if the entry contents exceeds the length limit.
                                return $state;
                            })
                            ->markdown(),
                        Components\TextEntry::make('metas')
                            ->label('Metas')
                            ->tooltip(function (Components\TextEntry $component): ?string {
                                $state = $component->getState();

                                if (strlen($state) <= $component->getCharacterLimit()) {
                                    return null;
                                }

                                // Only render the tooltip if the entry contents exceeds the length limit.
                                return $state;
                            }),
                    ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SubprogramaRelationManager::class,
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

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return ProgramaResource::getUrl('view', ['record' => $record]);
    }
}
