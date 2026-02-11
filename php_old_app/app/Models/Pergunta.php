<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pergunta extends Model
{
    use HasFactory;

    protected $table = 'perguntas';

    protected $fillable = [
        'formulario_id',
        'numero_da_pergunta',
        'pergunta',
    ];

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'formulario_id', 'id');
    }

    public function variaveis()
    {
        return $this->belongsToMany(Variavel::class, 'pergunta_variavel', 'pergunta_id', 'variavel_id');
    }

}