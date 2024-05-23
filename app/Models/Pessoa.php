<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pessoa extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'nome',
        'email',
        'bi',
        'data_nascimento',
        'genero',
        'tipo_pessoa',
        'grau_academico',
        'morada',
        'telefone',
        'observacoes',
        'id_criador', 
    ];

    public function programas()
    {
        return $this->belongsToMany(ProgramaPessoa::class, 'programa_pessoa');
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'id_criador');
    }

    public function subprograma()
    {
        return $this->hasMany(Subprograma::class, 'id', 'id_subprograma');
    }

    public function programa()
    {  
        return $this->hasMany(Programa::class, 'id');
    }

    public function subprogramapessoa()
    {
        return $this->hasMany(SubprogramaPessoa::class,  'id_pessoa', 'id');
    }

    public function pessoa()
    {
        return $this->HasMany (Pessoa::class, 'id');
    }
 

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['nome', 'email', 'bi', 'data_nascimento', 'genero', 'grau_academico', 'morada', 'telefone']);
        // Chain fluent methods for configuration options
    }
}

