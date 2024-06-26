<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gasto extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_programa',
        'id_orcamento',
        'id_subprograma',
        'id_subprograma_pessoa',
        'valor_gasto',
        'created_at',
        'updated_at',
    ];

    public function programa()
  {
    return $this->belongsTo(Programa::class, 'id_programa');
  }

  public function subprograma()
  {
    return $this->belongsTo(Subprograma::class, 'id_subprograma');
  }

  public function orcamento()
  {
    return $this->belongsTo(Orcamento::class, 'id_orcamento');
  }

}
