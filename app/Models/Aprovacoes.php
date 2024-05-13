<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aprovacoes extends Model
{
    use HasFactory;

    protected $table = 'aprovacoes';

    protected $fillable = [
        'workflow_orcamento_id',
        'passo_workflow_id',
        'usuario_id',
        'status',
        // Adicione outros campos, se necessário
    ];

    public function workflow()
    {
        return $this->belongsTo(WorkflowOrcamento::class, 'workflow_orcamento_id');
    }

    public function passo()
    {
        return $this->belongsTo(PassosWorkflow::class, 'passo_workflow_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
