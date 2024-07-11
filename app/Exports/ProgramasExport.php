<?php

namespace App\Exports;

use App\Models\Programa;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProgramasExport implements FromCollection, WithMapping
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
        ];
    }

    
}
