<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gasto extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_programa',
        'id_orcamento',
        'id_subprograma',
        'valor_gasto',
        'created_at',
        'updated_at',
    ];
}
