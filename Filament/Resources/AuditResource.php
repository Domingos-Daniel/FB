<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditResource\Pages;
use App\Filament\Resources\AuditResource\RelationManagers;
use App\Models\Audit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rmsramos\Activitylog\Resources\ActivitylogResource;

class AuditResource extends ActivitylogResource
{
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                static::getLogNameColumnCompoment(),
                static::getEventColumnCompoment(),
                static::getSubjectTypeColumnCompoment()
                ->toggleable(isToggledHiddenByDefault: true),
                static::getCauserNameColumnCompoment(),
                static::getPropertiesColumnCompoment(),
                static::getCreatedAtColumnCompoment(),
            ])
            ->filters([
                static::getDateFilterComponent(),
                static::getEventFilterCompoment(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAudits::route('/'),
            'view'  => Pages\ViewAudit::route('/{record}'),
        ];
    }
}
