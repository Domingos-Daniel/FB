<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowStage extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'descricao'];

    public function workflowItems()
    {
        return $this->hasMany(WorkflowItem::class, 'etapa_atual_id');
    }

    public function workflowTransitions()
    {
        return $this->hasMany(WorkflowTransition::class);
    }
} 
