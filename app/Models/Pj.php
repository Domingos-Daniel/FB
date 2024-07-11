<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pj extends Model
{
    use HasFactory;
    protected $table = 'projectos';

    protected $fillable = [
        'numero_projeto',
        'denominacao_projeto',
        'financiador_projeto',
        'provincia',
        'localidade',
        'entidade_executante',
        'entidade_beneficiaria',
        'ano_inicio',
        'ano_conclusao',
        'situacao_atual',
        'custo_total_projeto',
        'desembolso',
        'execucao_fisica',
        'data_inauguracao_previsao',
        'impacto_numerico',
        'sector',
    ];

}
