<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Orcamento;

class Subprograma extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'id_programa', 
        'designacao', 
        'valor', 
        'created_at', 
        'updated_at'
    ];

    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class, 'id_orcamento');
    }

  

    public function OrcamentoPrograma()
    {
        return $this->hasOne(OrcamentoPrograma::class, 'id_programa', 'id_programa');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'id_programa');
    }
}
