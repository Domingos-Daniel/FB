<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Programas extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?int $navigationSort = 3;
    
    protected static ?string $modelLabel = 'Gestão';
}
