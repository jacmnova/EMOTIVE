<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteFormulario extends Model
{
    use SoftDeletes;

    protected $table = 'cliente_formulario';

    protected $fillable = [
        'cliente_id',
        'formulario_id',
        'quantidade',
        'ativo',
        'deleted_by'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'deleted_by' => 'integer',
        'cliente_id' => 'integer',
        'formulario_id' => 'integer',
        'quantidade' => 'integer',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'formulario_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('withRelations', function ($query) {
            $query->with(['cliente', 'formulario']);
        });
    }

}