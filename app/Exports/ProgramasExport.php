<?php

namespace App\Exports;

use App\Models\Programa;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProgramasExport implements FromCollection, WithMapping, WithHeadings
{
    
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct(public Collection $records){
        //$this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    public function map($programa): array
    {
        return [
            $programa->nome,
            $programa->objetivo,
            $programa->responsavel,
        ];
    }

    public function headings(): array
    {
        return [
            'Nome do Programa',
            'Objetivo',
            'Responsável',
        ];
    }
}
