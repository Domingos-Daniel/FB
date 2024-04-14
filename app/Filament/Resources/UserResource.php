<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

use function PHPUnit\Framework\callback;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $modelLabel = 'Utilizador';
    protected static ?string $pluralModelLabel = 'Gestão Utilizadores';

    protected static ?string $navigationGroup = 'Configurações';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255)->label("Nome Completo"),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255), 
                //Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255)->label("Senha"),

                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name', function (Builder $query) {
                        return auth()->user()->hasRole('Admin') ? $query : $query->where('name', '!=', 'Admin');
                    })
                    ->multiple()
                    ->searchable()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->preload(),

            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->label("Nome Completo"),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Função')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime(format: "d/m/Y H:i:s")
                    ->sortable()->label("Data Validado"),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(format: "d/m/Y H:i:s")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(format: "d/m/Y H:i:s")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles.name')
                    ->relationship('roles', 'name')
                    ->options(function () {
                        return Role::pluck('name', 'id')->toArray();
                    })
                    ->label('Função')
                    ->multiple() // Habilitar multi-seleção
                    ->placeholder('Pesquisar funções...'), // Texto de placeholder na caixa de texto


            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            ActivitylogRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return auth()->user()->hasRole('Admin')
            ? parent::getEloquentQuery()
            : parent::getEloquentQuery()->whereHas(
                'roles',
                fn (Builder $query) => $query->where('name', '!=', 'Admin')
            );
    }
}
