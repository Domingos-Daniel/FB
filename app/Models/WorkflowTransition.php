<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class WorkflowTransition extends Model
{
    use HasFactory;

    protected $fillable = ['etapa_origem_id', 'etapa_destino_id', 'permissao_requerida'];

    public function etapaOrigem()
    {
        return $this->belongsTo(WorkflowStage::class, 'etapa_origem_id');
    }

    public function workflowStage()
    {
        return $this->belongsTo(WorkflowStage::class);
    }

    public function workflowItem(){
        return $this->hasMany(WorkflowItem::class);
    }

    public function etapaDestino()
    {
        return $this->belongsTo(WorkflowStage::class, 'etapa_destino_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }
}
