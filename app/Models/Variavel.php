<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CalculaRangosVariavel;

class Variavel extends Model
{
    use HasFactory, CalculaRangosVariavel;

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

        'r_baixa',
        'r_moderada',
        'r_alta',

        'd_baixa',
        'd_moderada',
        'd_alta',
        
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

    public function perguntas()
    {
        return $this->belongsToMany(Pergunta::class, 'pergunta_variavel');
    }
    
    /**
     * Boot del modelo - actualiza rangos automáticamente cuando se actualizan las preguntas
     */
    protected static function boot()
    {
        parent::boot();
        
        // Actualizar rangos después de guardar, pero solo si cambió algo relevante
        static::saved(function ($variavel) {
            // Evitar bucle infinito: solo actualizar si los rangos necesitan cambio
            $variavel->load('perguntas', 'formulario');
            $totalPerguntas = $variavel->perguntas()->count();
            $scoreFim = $variavel->formulario->score_fim ?? 6;
            $rangos = static::calcularRangosGenerales($totalPerguntas, $scoreFim);
            
            // Solo actualizar si los valores son diferentes
            if ($variavel->B != $rangos['B'] || $variavel->M != $rangos['M'] || $variavel->A != $rangos['A']) {
                // Usar update sin eventos para evitar bucle
                static::withoutEvents(function() use ($variavel, $rangos) {
                    $variavel->update([
                        'B' => $rangos['B'],
                        'M' => $rangos['M'],
                        'A' => $rangos['A']
                    ]);
                });
            }
        });
    }
}