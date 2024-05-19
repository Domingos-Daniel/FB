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
        'id_criador',
        'created_at', 
        'updated_at'
    ];

    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class, 'id_orcamento');
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'id_criador');
    }

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'id_pessoa');
    }

    public function gasto()
    {
      return $this->hasMany(Gasto::class);
    }
    

    public function OrcamentoPrograma()
    {
        return $this->hasOne(OrcamentoPrograma::class, 'id_programa', 'id_programa');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class,  'id_programa', 'id');
    }
}
