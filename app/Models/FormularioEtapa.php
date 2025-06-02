<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioEtapa extends Model
{
    use HasFactory;

    protected $table = 'formulario_etapas';

    protected $fillable = [
        'formulario_id',
        'etapa',
        'de',
        'ate',
    ];

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'formulario_id');
    }
}
