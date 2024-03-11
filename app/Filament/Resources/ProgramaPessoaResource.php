<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ProgramaPessoaExporter;
use App\Filament\Resources\ProgramaPessoaResource\Pages;
use App\Filament\Resources\ProgramaPessoaResource\RelationManagers;
use App\Filament\Resources\ProgramaPessoaResource\RelationManagers\PessoaRelationManager;
use App\Models\Pessoa;
use App\Models\Programa;
use App\Models\ProgramaPessoa;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;

class ProgramaPessoaResource extends Resource
{
    protected static ?string $model = ProgramaPessoa::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $modelLabel = 'Financiamento';
    protected static ?string $pluralModelLabel = 'Gestão de Financiamentos';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        $programas = Programa::pluck('nome', 'id')->toArray();
        $pessoas = Pessoa::pluck('nome', 'id')->toArray();

        return $form
        ->schema([
                
            Forms\Components\Select::make('pessoa_id')
                ->options($pessoas)
                ->label("Selecione o Solicitante")
                ->searchable()
                ->required(fn (string $context): bool => $context === 'create'),
            Forms\Components\Select::make('programa_id')
                ->options($programas)
                ->searchable()
                ->label("Selecione o Programa Social a ser atribuido")
                ->required(fn (string $context): bool => $context === 'create'),
                
            Forms\Components\DatePicker::make('data_inicio')
            ->required(fn (string $context): bool => $context === 'create')
            ->closeOnDateSelection(),
            
            Forms\Components\DatePicker::make('data_fim')
            ->required(fn (string $context): bool => $context === 'create')
            ->closeOnDateSelection(),

            Forms\Components\Select::make('status')
            ->options([
                'Pendente' => 'Pendente',
                'Visto' => 'Visto',
                'Aprovado' => 'Aprovado',
                'Reprovado' => 'Reprovado',
            ])
            ->label("Qual o status deste processo")
            ->required(fn (string $context): bool => $context === 'create')
            ->preload()
            ->searchable()
            ->disableOptionWhen(function ($value, $record) {
                // Aqui você deve verificar o status atual do registro na base de dados
                // e desativar a opção correspondente
                $statusAtual = $record->status ?? null;
        
                // Desativa a opção atual somente se houver um status atual definido
                // e o valor da opção for igual ao status atual
                return $statusAtual !== null && $value === $statusAtual;
            }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pessoa.nome')
                    ->label('Pessoa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('programa.nome')
                    ->label('Programa Associado')
                    ->sortable(),
                Tables\Columns\TextColumn::make('programa.orcamento')
                    ->label('Orçamento do Programa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_inicio')
                    ->label('Data de Início')
                    ->dateTime(format: 'd/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_fim')
                    ->label('Data de Fim')
                    ->dateTime(format: 'd/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->multiple()
                    ->searchable()
                    ->options([
                        'Pendente' => 'Pendente',
                        'Visto' => 'Visto',
                        'Aprovado' => 'Aprovado',
                        'Reprovado' => 'Reprovado',
                        
                    ])
                    ->default('Pendente'),
                    
                    Filter::make('data_inicio')
                        ->form([
                            DatePicker::make('data_inicio'),
                            DatePicker::make('data_fim'),
                        ])
                        ->query(function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['data_inicio'],
                                    fn (Builder $query, $date): Builder => $query->whereDate('data_inicio', '>=', $date),
                                )
                                ->when(
                                    $data['data_fim'],
                                    fn (Builder $query, $date): Builder => $query->whereDate('data_inicio', '<=', $date),
                                );
                        })
            ])
            
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('status')
                ->icon('heroicon-m-arrow-path')
                ->color('warning')
                ->form([
                    Select::make('status')
                        ->options([
                            'Pendente' => 'Pendente',
                            'Visto' => 'Visto',
                            'Aprovado' => 'Aprovado',
                            'Reprovado' => 'Reprovado',
                        ])
                        ->label("Qual o status deste processo")
                        ->required(fn (string $context): bool => $context === 'create')
                        ->preload()
                        ->searchable()
                        ->disableOptionWhen(function ($value, $record) {
                            // Aqui você deve verificar o status atual do registro na base de dados
                            // e desativar a opção correspondente
                            $statusAtual = $record->status ?? null;
                    
                            // Desativa a opção atual somente se houver um status atual definido
                            // e o valor da opção for igual ao status atual
                            return $statusAtual !== null && $value === $statusAtual;
                        }),
                    
                ])
                ->action(function(ProgramaPessoa $programapessoa, $data): void {
                    $programapessoa->status = $data['status'];
                    $programapessoa->save();
                })


                // EditAction::make()
                // ->record($this->post)
                // ->form([
                //     TextInput::make('title')
                //         ->required()
                //         ->maxLength(255),
                //     // ...
                // ]),
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                    ->label('Exportar Dado(s)')
                    ->exporter(ProgramaPessoaExporter::class)
                    ->columnMapping(true)
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            PessoaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProgramaPessoas::route('/'),
            'create' => Pages\CreateProgramaPessoa::route('/create'),
            'edit' => Pages\EditProgramaPessoa::route('/{record}/edit'),
        ];
    }
}
