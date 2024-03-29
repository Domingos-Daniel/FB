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
    ];

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
        return $this->hasOne(Orcamento::class, 'id_orcamento');
    }

    public function subprogramas()
    {
        return $this->hasMany(Subprograma::class);
    }

    public function pessoa() 
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['nome', 'email', 'status', 'id_orcamento', 'responsavel', 'data_fim', 'data_inicio']);
        // Chain fluent methods for configuration options
    }
}
