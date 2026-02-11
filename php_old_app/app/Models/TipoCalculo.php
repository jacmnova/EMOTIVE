<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCalculo extends Model
{
    use HasFactory;

    protected $table = 'tipo_calculo';

    protected $fillable = [
        'nome',
        'descricao',
        'operador',
        'formula',
    ];

    protected $casts = [
        // Se desejar, pode definir castings, por exemplo:
        // 'created_at' => 'datetime',
    ];
}