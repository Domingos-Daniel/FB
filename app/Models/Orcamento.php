<?php

namespace App\Models;

use EightyNine\Approvals\Models\ApprovableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orcamento extends ApprovableModel
{
    use HasFactory;

    protected $fillable = [
        'id_programa',
        'valor',
        'descricao',
        'created_at',
        'updated_at',
    ];
   
    // Recupera os IDs dos registros de Orcamento que passaram pela terceira etapa do workflow
    public function workflowItensAprovados()
    {
        // Retorne apenas os registros do modelo principal
        // que têm um item de workflow com etapa_atual_id igual a 3
        return $this->workflowItens()->where('etapa_atual_id', 3);
    }

    public function obterRegistrosAprovados()
{
    // Consulta para recuperar registros aprovados no workflow
    $registrosAprovados = WorkflowItem::where('etapa_atual_id', 3)->get();

    // Faça algo com os registros aprovados...
}
    public function workflowItem()
    {
        return $this->morphOne(WorkflowItem::class, 'modelo');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'id_programa');
    }
 
    public function subprograma()
    {
        return $this->hasMany(Subprograma::class, 'id_subprograma');
    } 
    
    public function orcamentoProgramas()
    {
        return $this->hasMany(OrcamentoPrograma::class);
    }

}
