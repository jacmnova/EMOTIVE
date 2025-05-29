<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Midia extends Model
{
    protected $table = 'midias';

    protected $fillable = [
        'titulo',
        'tipo',
        'url',
        'arquivo',
        'formulario_id'
    ];

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'formulario_id');
    }



}
