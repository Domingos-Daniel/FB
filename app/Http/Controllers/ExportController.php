<?php

namespace App\Http\Controllers;


use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CombinedExport;
use App\Exports\ProjectsExport;

class ExportController extends Controller
{
    public function exporter()
    {
        return Excel::download(new ProjectsExport, 'Planilha Modelo Fundação Brilhante de '.date('d-m-Y H:i:s').'.xlsx');
    }
}
