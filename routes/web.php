<?php

use App\Exports\ProjectsExport;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', '/admin');
//Route::get('export-combined', [ExportController::class, 'export']);
Route::get('export', [ExportController::class, 'exporter']);
Route::get('/download-projects-excel', function () {
    return Excel::download(new ProjectsExport, 'Planilha Modelo Fundação Brilhante de '.date('d-m-Y H:i:s').'.xlsx');
})->name('download-projects-excel');
