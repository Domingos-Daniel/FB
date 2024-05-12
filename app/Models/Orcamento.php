<?php

namespace App\Models;

use EightyNine\Approvals\Models\ApprovableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orcamento extends ApprovableModel
{
    use HasFactory;

    protected $fillable = [
        'valor',
        'descricao',
        'id_criador',
        'created_at',
        'updated_at',
    ];

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'id_programa');
    }
 
    public function subprograma()
    {
        return $this->hasMany(Subprograma::class, 'id_subprograma');
    } 
    
    public function orcamentoProgramas()
    {
        return $this->hasMany(OrcamentoPrograma::class);
    }

}
