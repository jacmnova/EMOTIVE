<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'usuario_id',
        'tipo',
        'cpf_cnpj',
        'nome_fantasia',
        'razao_social',
        'logo_url',
        'email',
        'contato',
        'telefone',
        'ativo',
        'deleted_by'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'deleted_by' => 'integer',
        'usuario_id' => 'integer'
    ];

    public function getFormattedCnpjAttribute()
    {
        $cpf_cnpj = $this->cpf_cnpj;
        return preg_replace("/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/", "$1.$2.$3/$4-$5", $cpf_cnpj);
    }

    public function getFormattedCpfAttribute()
    {
        $cpf_cnpj = $this->cpf_cnpj;
        return preg_replace("/^(\d{3})(\d{3})(\d{3})(\d{2})$/", "$1.$2.$3-$4", $cpf_cnpj);
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

}