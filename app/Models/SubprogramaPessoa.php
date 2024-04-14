<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubprogramaPessoa extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_programa',
        'id_subprograma',
        'id_pessoa',
        'description',
        'data_inicio',
        'data_fim',
    ];

 
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class,  'id_pessoa', 'id');
    }

    public function subprograma()
    {
        return $this->hasMany(Subprograma::class, 'id', 'id_subprograma');
    } 

    public function programa()
    {
        return $this->hasMany(Programa::class, 'id', 'id_programa');
    }

    public function subprogramapessoa()
    {
        return $this->belongsTo(SubprogramaPessoa::class, 'id');
    }
}
