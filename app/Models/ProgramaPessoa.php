<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProgramaPessoa extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'programa_id',
        'pessoa_id',
        'nivel_acesso',
        'status',
        'data_inicio',
        'data_fim',
        'id_criador',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    } 

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['programa_id', 'pessoa_id', 'status', 'data_inicio', 'data_fim']);
        // Chain fluent methods for configuration options
    }

}
