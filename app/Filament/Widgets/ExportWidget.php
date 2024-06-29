<?php

namespace App\Filament\Widgets;

use App\Exports\ProjectsExport;
use Filament\Actions\Action;
use Filament\Widgets\Widget;
use Maatwebsite\Excel\Facades\Excel;

class ExportWidget extends Widget
{
    protected static string $view = 'filament.widgets.export-widget';
    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    public function mountAction(): void
    {
        // Perform any necessary setup or data fetching for the widget
    }

    public function downloadExcel()
    {
        return redirect()->route('download-projects-excel');
    }
}
