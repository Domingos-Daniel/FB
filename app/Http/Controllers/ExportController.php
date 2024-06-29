<?php

namespace App\Http\Controllers;


use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CombinedExport;
use App\Exports\ProjectsExport;

class ExportController extends Controller
{
    public function export()
    {
        return Excel::download(new CombinedExport, 'combined_export.xlsx');
    }

    public function exporter()
    {
        return Excel::download(new ProjectsExport, 'programas.xlsx');
    }
}
