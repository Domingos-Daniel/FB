<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowOrcamento extends Model
{
    use HasFactory;

    protected $table = 'workflow_orcamento';

    protected $fillable = [
        'orcamento_id',
        'status',
        'prox_passo',
        'num_aprovacoes_necessarias',
        'motivo_rejeicao',
        'id_criador',
        'processado_por'
    ];

    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class);
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'id_criador');
    }

    public function processadopor()
    {
        return $this->belongsTo(User::class, 'processado_por');
    }

    public function approve()
    {
        // Verifica se o estado atual já é aprovado
        if ($this->status === 'aprovado') {
            // Se já estiver aprovado, retorna true
            return true;
        }

        // Define o estado como aprovado
        $this->status = 'aprovado';

        // Salva as mudanças no banco de dados
        return $this->save();
    }
}
