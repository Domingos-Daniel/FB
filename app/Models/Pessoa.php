<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'email',
        'bi',
        'data_nascimento',
        'genero',
        'grau_academico',
        'morada',
        'telefone',
    ];

    public function programas()
    {
        return $this->belongsToMany(Programa::class, 'programa_pessoa');
    }
}

