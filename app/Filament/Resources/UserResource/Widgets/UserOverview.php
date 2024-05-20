<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class UserOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected $listeners = ['updateUserOverview' => '$refresh'];
    protected static bool $isLazy = false;
 
    protected function getCards(): array
    {
        $rolesCount = DB::table('users')
        ->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN roles.name = "Admin" THEN 1 ELSE 0 END) AS admin,
            SUM(CASE WHEN roles.name != "Admin" THEN 1 ELSE 0 END) AS active
        ')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->first();
 
        return [
            Stat::make('Total', $rolesCount->total)
                ->color('warning')
                ->icon('heroicon-s-user-group')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-user-group')
                ->description('Total de Utilizadores'),
 
            Stat::make('Admin', $rolesCount->admin)
                ->color('success')
                ->icon('heroicon-s-shield-check')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-shield-check')
                ->description('Total de Administradores'),
 
            Stat::make('Active', $rolesCount->active)
                ->color('info')
                ->icon('heroicon-s-user')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon('heroicon-m-user')
                ->description('Utilizadores Padrão'),
        ];
    }
}
