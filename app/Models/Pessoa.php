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
        'grau_academico',
        'morada',
        'telefone', 
    ];

    public function programas()
    {
        return $this->belongsToMany(ProgramaPessoa::class, 'programa_pessoa');
    }

   

    public function programa()
    { 
        return $this->belongsTo(Programa::class);
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['nome', 'email', 'bi', 'data_nascimento', 'genero', 'grau_academico', 'morada', 'telefone']);
        // Chain fluent methods for configuration options
    }
}

