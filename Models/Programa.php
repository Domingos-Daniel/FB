<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Programa extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome',
        'descricao',
        'area_foco',
        'publico_alvo',
        'objetivo',
        'metas',
        'orcamento',
        'data_inicio',
        'data_fim',
        'responsavel',
        'status',
    ];

    public function getValidoAttribute()
    {
        return $this->data_fim >= Carbon::now();
    }
    public function pessoas()
    {
        return $this->belongsToMany(Pessoa::class, 'programa_pessoa');
    }
}
