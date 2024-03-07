<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaPessoa extends Model
{
    use HasFactory;
    protected $fillable = [
        'programa_id',
        'pessoa_id',
        'nivel_acesso',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_id');
    }

}
