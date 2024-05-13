<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassosWorkflow extends Model
{
    use HasFactory;

    protected $table = 'passos_workflow';

    protected $fillable = [
        'nome',
        'descricao',
        // Adicione outros campos, se necessário
    ];
}
