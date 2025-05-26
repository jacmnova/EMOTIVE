<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',

        'sa',
        'admin',
        'cliente_id',
        'gestor',
        'usuario',
        'ativo',
        'deleted_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'sa' => 'boolean',
        'admin' => 'boolean',
        'cliente_id' => 'integer',
        'gestor' => 'boolean',
        'usuario' => 'boolean',
        'ativo' => 'boolean',
        'password' => 'hashed', 
    ];

    public function adminlte_image()
    {
        if ($this->avatar && $this->avatar !== 'img/user.png') {
            return asset('storage/'.$this->avatar);
        } else {
            return asset('img/user.png');
        }
    }

    public function adminlte_desc()
    {
        return $this->email; 
    }

    public function adminlte_profile_url()
    {
        return '/dados';
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id');
    }

    public function getQuantidadeFormulariosAttribute()
    {
        return UsuarioFormulario::where('usuario_id', $this->id)->count();
    }

    public function getQuantidadeFormulariosPendentesAttribute()
    {
        return UsuarioFormulario::where('usuario_id', $this->id)
            ->where('status', 'pendente')
            ->count();
    }

    public function getQuantidadeFormulariosFinalizadosAttribute()
    {
        return UsuarioFormulario::where('usuario_id', $this->id)
            ->where('status', 'completo')
            ->count();
    }


}
