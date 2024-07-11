<?php

namespace App\Http\Controllers;

use App\Exports\PjExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PjController extends Controller
{
    public function export()
    {
        return Excel::download(new PjExport, 'PLANILHA MODELO MIREMPET de '.date('d-m-Y H:i:s').'.xlsx');
    }
}
