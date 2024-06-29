<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Subprograma;

class Programa extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'nome',
        'descricao',
        'area_foco',
        'publico_alvo',
        'objetivo',
        'metas',
        'responsavel', 
        'id_criador',
    ];

    protected $casts = [
        'area_foco' => 'array',
        'publico_alvo' => 'array',
    ];

    public function orcamentos()
    {
        return $this->belongsToMany(Orcamento::class, 'orcamento_programas', 'id_programa', 'id_orcamento');
    }

    public function responsavel()
    {
        return $this->belongsTo(Pessoa::class, 'id_criador');
    }

    public function getValidoAttribute()
    {
        return $this->data_fim >= Carbon::now();
    }
    public function pessoas()
    {
        return $this->belongsToMany(Pessoa::class, 'programa_pessoa');
    }

    public function orcamento()
    {
        return $this->hasOne(Orcamento::class, 'id');
    }

    public function subprograma()
    {
        return $this->hasMany(Subprograma::class,  'id_programa', 'id');
    }


    public function criador()
    {
        return $this->belongsTo(User::class, 'id_criador');
    }

    public function pessoa()  
    {
        return $this->belongsTo(Pessoa::class, 'id_pessoa', 'id');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'id');
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['nome', 'email', 'status', 'id_orcamento', 'responsavel', 'data_fim', 'data_inicio']);
        // Chain fluent methods for configuration options
    }
}
