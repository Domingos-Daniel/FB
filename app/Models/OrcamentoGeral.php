<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrcamentoGeral extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao', 
        'valor_total',
        'id_criador',
        'created_at',
        'updated_at'
    ];

    public function orcamentos()
    {
        return $this->hasMany(Orcamento::class);
    }
}
