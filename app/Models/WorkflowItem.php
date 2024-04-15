<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WorkflowItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'modelo_type', 
        'modelo_id', 
        'etapa_atual_id'
    ]; 

    public function modelo()
    {
        return $this->morphTo();
    }
     
    public function workflowStage()
    {
        return $this->belongsTo(WorkflowStage::class, 'etapa_atual_id');
    } 

    public function etapaAtual()
    {
        return $this->belongsTo(WorkflowStage::class, 'etapa_atual_id');
    }

    public function avancarEtapa()
    {
        $transicao = WorkflowTransition::where('etapa_origem_id', $this->etapa_atual_id)->first();

        if (!$transicao) {
            return false; // Não há transição disponível para avançar
        }

        if (!$this->usuarioPodeAvancar()) {
            return false; // Usuário não tem permissão para avançar
        }

        $this->update(['etapa_atual_id' => $transicao->etapa_destino_id]);

        return true;
    }

    public function retrocederEtapa()
    {
        $transicao = WorkflowTransition::where('etapa_destino_id', $this->etapa_atual_id)->first();

        if (!$transicao) {
            return false; // Não há transição disponível para retroceder
        }

        $this->update(['etapa_atual_id' => $transicao->etapa_origem_id]);

        return true;
    }

    public function workflowtransition(){
        return $this->hasMany(WorkflowTransition::class);
    }

    public function usuarioPodeAvancar()
    {
        $transicao = WorkflowTransition::where('etapa_origem_id', $this->etapa_atual_id)->first();

        if (!$transicao) {
            return false; // Não há transição disponível para avançar
        }

        // Verifique se o usuário atual tem a permissão necessária para avançar
        return Auth::user()->hasPermissionTo($transicao->permissao_requerida);
    }
}
