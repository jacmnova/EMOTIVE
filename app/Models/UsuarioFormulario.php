<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioFormulario extends Model
{
    use SoftDeletes;

    protected $table = 'usuario_formulario';

    protected $fillable = [
        'usuario_id',
        'formulario_id',
        'status',
        'data_limite',
        'video_assistido',
        'deleted_by',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'status' => 'string',
        'data_limite' => 'date',
        'video_assistido' => 'boolean',
        'deleted_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'formulario_id');
    }

    public function midia()
    {
        return $this->hasOne(Midia::class, 'formulario_id', 'formulario_id');
    }

}
