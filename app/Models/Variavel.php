<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variavel extends Model
{
    use HasFactory;

    protected $table = 'variaveis';

    protected $fillable = [
        'nome',
        'descricao',
        'tag',
        'B',
        'M',
        'A',
        'baixa',
        'moderada',
        'alta',
        'formulario_id',
    ];

    protected $casts = [
        'B' => 'integer',
        'M' => 'integer',
        'A' => 'integer',
    ];

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'formulario_id');
    }
}