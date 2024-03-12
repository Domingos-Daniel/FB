<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Orcamento;
class OrcamentoPrograma extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'id_programa',
        'id_orcamento',
        'created_at',
        'updated_at',
    ];

    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class, 'id_orcamento');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'id_programa');
    }

}
