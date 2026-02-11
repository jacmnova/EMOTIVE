<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Formulario;

class Resposta extends Model
{
    use HasFactory;

    protected $table = 'respostas';

    protected $fillable = [
        'user_id',
        'pergunta_id',
        'valor_resposta',
    ];

    public function pergunta()
    {
        return $this->belongsTo(Pergunta::class, 'pergunta_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'formulario_id', 'id');
    }

    public function variaveis()
    {
        return $this->belongsToMany(Variavel::class, 'pergunta_variavel', 'pergunta_id', 'variavel_id');
    }


}
