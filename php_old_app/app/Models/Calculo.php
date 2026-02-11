<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calculo extends Model
{
    protected $table = 'tipo_calculo';

    protected $fillable = [
        'nome',
        'descricao',
        'operador',
        'formula',
    ];
}