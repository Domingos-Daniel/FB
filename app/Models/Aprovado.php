<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aprovado extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_orcamento_id',
        'passo_workflow_id',
        'status',
        'usuario_id',
    ];

    public function workfloworcamento(){
        return $this->belongsTo(WorkflowOrcamento::class);
    }
}
