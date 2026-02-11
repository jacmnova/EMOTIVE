<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerguntaVariavel extends Model
{
    use HasFactory;

    protected $table = 'pergunta_variavel';

    protected $fillable = [
        'pergunta_id',
        'variavel_id',
    ];

    public function pergunta()
    {
        return $this->belongsTo(Pergunta::class);
    }

    public function variavel()
    {
        return $this->belongsTo(Variavel::class);
    }

}