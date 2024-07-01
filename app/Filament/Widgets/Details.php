<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class Details extends Widget
{

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    
    public function mount(): void
    {
        // Perform any necessary setup or data fetching for the widget
    }
    protected static string $view = 'filament.widgets.details';
}
