<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_programa',
        'valor',
        'descricao',
        'created_at',
        'updated_at',
    ];
    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function orcamentoProgramas()
    {
        return $this->hasMany(OrcamentoPrograma::class);
    }
}
