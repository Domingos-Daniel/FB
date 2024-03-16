<?php

namespace App\Filament\Clusters\Programas\Resources;

use App\Filament\Clusters\Programas;
use App\Filament\Clusters\Programas\Resources\SubprogramaResource\Pages;
use App\Filament\Clusters\Programas\Resources\SubprogramaResource\RelationManagers;
use App\Models\gasto;
use App\Models\Programa;
use App\Models\Orcamento;
use App\Models\OrcamentoPrograma;
use App\Models\Subprograma;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubprogramaResource extends Resource
{
    protected static ?string $model = Subprograma::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    
    protected static ?string $cluster = Programas::class;

    public static function form(Form $form): Form
    {
        $programas = Programa::pluck('nome', 'id')->toArray();
        $orcamentos = Orcamento::pluck('valor', 'id')->toArray();

        return $form
            ->schema([
                Forms\Components\Select::make('id_programa')
                    ->options($programas)
                    ->searchable()
                    ->label("Selecione o Programa Social")
                    ->preload()
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\TextInput::make('designacao')
                    ->label("Designação")
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('valor')
                    ->required()
                    ->numeric(),
                // Forms\Components\Select::make('id_orcamento')
                //     ->options($orcamentos)
                //     ->label("Selecione o Orçamento")
                //     ->preload()
                //     ->searchable()
                //     ->required(fn (string $context): bool => $context === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_programa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('designacao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('programa.nome')
                    ->label('Programa Associado')
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
            'index' => Pages\ManageSubprogramas::route('/'),
        ];
    }

    public function beforeCreate($resource)
    {
        $orcamentoPrograma = OrcamentoPrograma::where('id_programa', $resource->id_programa)->first();
    
        if ($orcamentoPrograma) {
            if ($resource->valor < $orcamentoPrograma->valor) {
                $diferenca = $orcamentoPrograma->valor - $resource->valor;
    
                Gasto::create([
                    'program_id' => $resource->id_programa,
                    'subprogram_id' => $resource->getKey(),
                    'orcamento_id' => $orcamentoPrograma->id,
                    'valor_gasto' => $diferenca,
                ]);

                Notification::make()
                ->warning()
                ->title('You don\'t have an active subscription!')
                ->body('Choose a plan to continue.')
                ->persistent()
                ->actions([
                    Action::make('subscribe')
                        ->button()
                        ->url(route('subscribe'), shouldOpenInNewTab: true),
                ])
                ->send();
    
                return parent::onSave($resource);
            } else {
                throw new \Exception("O valor do subprograma deve ser menor que o valor do programa.");
            }
        } else {
            throw new \Exception("OrcamentoPrograma não encontrado para o programa especificado.");
        }
    }
    
}
